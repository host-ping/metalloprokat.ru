<?php

namespace Metal\DemandsBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixDemandsCollisionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:demands:fix-collisions');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $doctrine = $this->getContainer()->get('doctrine');
        $categoryService = $this->getContainer()->get('metal.categories.category_matcher');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */

        $em->getRepository('MetalProjectBundle:Site')->disableLogging();
        $conn = $em->getConnection();

        $conn->executeUpdate('UPDATE demand_item SET volume_type = :type WHERE volume_type IS NULL', array('type' => 0));

        // обнуляем category_id у тех заявок, у которых нет demand_item с выбранной категорией
        $conn->executeUpdate('
            UPDATE
                demand d
                SET d.category_id = NULL
                WHERE
                  NOT EXISTS (
                    SELECT di.id
                    FROM demand_item di
                    WHERE di.demand_id = d.id
                    AND di.category_id = d.category_id
                )
                AND deleted_at IS NULL
                AND d.category_id IS NOT NULL
                ');

        $conn->executeQuery('DELETE dc FROM demand_category dc WHERE NOT EXISTS (SELECT di.id FROM demand_item di WHERE di.category_id = dc.category_id)');

        // Находим все позиции заявок без категории.
        $demandItems = $conn->executeQuery('SELECT di.id, di.demand_id, di.title FROM demand_item di WHERE di.category_id IS NULL ORDER BY id')->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($demandItems as $demandItem) {
            if (!$demandItem['title']) {
                $output->writeln(sprintf('%s: Demand Item %d: no title.', date('d.m.Y H:i:s'), $demandItem['id']));
                continue;
            }

            $category = $categoryService->getCategoryByTitle($demandItem['title']);

            if (!$category) {
                $output->writeln(sprintf('%s: Demand Item %d: no category found.', date('d.m.Y H:i:s'), $demandItem['id']));
                continue;
            }

            $output->writeln(
                sprintf(
                    '%s: Demand id %d - %s: found category %d, category title: %s',
                    date('d.m.Y H:i:s'),
                    $demandItem['demand_id'],
                    $demandItem['title'],
                    $category->getId(),
                    $category->getTitle()
                )
            );

            $conn->update('demand_item', array('category_id' => $category->getId()), array('id' => $demandItem['id']));
        }

        // Обновляем таблицу demand_category на основании demand_item
        $conn->executeQuery(
            'INSERT IGNORE INTO demand_category (demand_id, category_id)
                                SELECT di.demand_id, di.category_id
                                  FROM demand_item AS di
                                  JOIN demand AS d ON di.demand_id = d.id
                                  WHERE di.category_id IS NOT NULL
                                  AND d.deleted_at IS NULL
                                  GROUP BY di.demand_id, di.category_id
                                ');

        $demands = $conn->fetchAll('SELECT id FROM demand WHERE category_id IS NULL');
        foreach ($demands as $demand) {
            // Ищем для заявки категорию которая есть у demand_item с сортировкой по количеству.
            $demandItemCategory = $conn->fetchColumn(
                'SELECT category_id FROM demand_item WHERE demand_id = :demand_id AND category_id IS NOT NULL GROUP BY category_id ORDER BY COUNT(*) DESC LIMIT 1',
                array(
                    'demand_id' => $demand['id']
                )
            );

            if ($demandItemCategory) {
                $output->writeln(sprintf('%s: Found category: %d for demand: %d', date('d.m.Y H:i:s'), $demandItemCategory, $demand['id']));

                $conn->update('demand', array('category_id' => $demandItemCategory), array('id' => $demand['id']));
            } else {
                // Если у заявки нету не одной позиции с категорией то отлючаем завяку и удаляем demand_category.
                $output->writeln(sprintf('%s: Category not found, disable demand: %d', date('d.m.Y H:i:s'), $demand['id']));
                $conn->executeUpdate('UPDATE demand SET moderated_at = NULL WHERE id = :demand_id',
                    array(
                        'demand_id' => $demand['id']
                    )
                );
            }
        }

        $output->writeln(sprintf('%s: Finish command.', date('d.m.Y H:i:s')));
    }
}
