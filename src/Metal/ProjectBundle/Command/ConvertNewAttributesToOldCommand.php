<?php

namespace Metal\ProjectBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertNewAttributesToOldCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:convert-2old-attributes');
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
        $this->addOption('category-id', null, InputOption::VALUE_OPTIONAL);
        $this->addOption('truncate-category', null, InputOption::VALUE_NONE);

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

//$output->writeln($input->getOption('category-id')); die();
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $conn = $em->getConnection();

        $em->getRepository('MetalProjectBundle:Site')->disableLogging();

        if ($input->getOption('truncate')) {
            $conn->executeQuery('SET FOREIGN_KEY_CHECKS = 0');
            $conn->executeQuery('TRUNCATE TABLE Message155');
            $conn->executeQuery('TRUNCATE TABLE Message157');
            $conn->executeQuery('TRUNCATE TABLE Message162');
            $conn->executeQuery('TRUNCATE TABLE parameters_types_priorities');
            $conn->executeQuery('SET FOREIGN_KEY_CHECKS = 1');
            $output->writeln('Tables truncated');
        }

        $conn->executeQuery('
            INSERT IGNORE INTO Message155 (Message_ID, name, Type, Keyword, slug, sinonym, Steel)
              SELECT id, value, attribute_id, slug, slug, regex_match, additional_info FROM attribute_value'
        );

        $conn->executeQuery('
            INSERT IGNORE INTO parameters_types_priorities (id, priority, title)
            SELECT id, url_priority, title FROM attribute'
        );

        // parameter-category
        if ($input->getOption('category-id')){
            $resGr = $conn->fetchAll('
                  SELECT
                  DISTINCT(`category_id`) cid, attribute_id, a.title, a.url_priority
                  FROM `attribute_value_category` avc
                  JOIN attribute_value av on av.id=`attribute_value_id`
                  JOIN attribute a ON a.id = attribute_id
                  WHERE `category_id` = :razd ',
              array('razd' => $input->getOption('category-id'))
            );
            if ($input->getOption('truncate-category')) {
                $conn->executeQuery('
                DELETE FROM Message162 
                WHERE Parent_Razd IN 
                  (SELECT Message_ID 
                  FROM Message157 WHERE
                  Parent_Razd=:razd)',
                    array('razd' => $input->getOption('category-id'))
                );
                $conn->executeQuery('
                DELETE FROM Message157 
                WHERE  Parent_Razd=:razd',
                    array('razd' => $input->getOption('category-id'))
                );
            }
        }else{
            $resGr = $conn->fetchAll('SELECT
            DISTINCT(`category_id`) cid, attribute_id, a.title, a.url_priority
              FROM `attribute_value_category` avc
            JOIN attribute_value av on av.id=`attribute_value_id`
            JOIN attribute a ON a.id = attribute_id
            ORDER BY `avc`.`category_id` ');
        }

        foreach ($resGr as $group) {
            $output->writeln(sprintf('Category_ID - "%s", attribute - %s', $group['cid'], $group['attribute_id']));
            $gr = $conn->fetchColumn('SELECT Message_ID FROM Message157 WHERE Type = :type AND Parent_Razd=:razd',
                array('type' => $group['attribute_id'], 'razd' => $group['cid'])
            );

            if (!$gr) {
                $conn->executeQuery('
                    INSERT INTO Message157 (User_ID, Subdivision_ID, Sub_Class_ID,Keyword, Created,LastUser_ID, Parent_Razd, Type, Name, PriorityShow)
                    VALUES (0, 0, 0, \'\', now(), 0, :razd, :type, :name, :urlpriority)',
                    array(
                        'razd' => $group['cid'],
                        'type' => $group['attribute_id'],
                        'name' => $group['title'],
                        'urlpriority' => $group['url_priority'],
                    )
                );
                $gr = $conn->lastInsertId();
            }

            $parGrs = $conn->fetchAll('
                SELECT
                avc.id,`category_id`,`attribute_value_id`,attribute_id
                FROM
                `attribute_value_category` avc
                JOIN attribute_value av ON av.id=`attribute_value_id`
                WHERE `category_id` = :razd AND attribute_id = :type',
                array('razd' => $group['cid'], 'type' => $group['attribute_id'])
            );

            foreach ($parGrs as $par) {
                $conn->executeQuery('
                    INSERT IGNORE INTO Message162
                    (Message_ID, Parent_Razd, Par_ID)
                    VALUES
                    (:id, :razd, :par)',
                    array('id' => $par['id'], 'razd' => $gr, 'par' => $par['attribute_value_id'])
                );
            }
        }

        $em->getRepository('MetalProjectBundle:Site')->restoreLogging();

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
