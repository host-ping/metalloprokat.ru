<?php

namespace Metal\CategoriesBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MergeCategoriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:categories:merge-categories');
        $this->addArgument('main-id', InputArgument::REQUIRED);
        $this->addArgument('additional-id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

//        160 - оставляем
//        367 - удаляем

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        /* @var $em EntityManager */
        $conn = $em->getConnection();
        $archiveConn = $this->getContainer()->get('doctrine.dbal.archive_connection');
        $mainCategoryId = $input->getArgument('main-id');
        $additionalCategoryId = $input->getArgument('additional-id');

        $categoriesIds = array($mainCategoryId, $additionalCategoryId);

        $categories = $em->getRepository('MetalCategoriesBundle:Category')
            ->findBy(array('id' => $categoriesIds));

        // проверяем на существования обеих категорий
        if (count($categories) !== 2) {
            if (!$categories) {
                throw new \InvalidArgumentException('Категории не существуют');
            }

            $diff = array_diff($categoriesIds, array(reset($categories)->getId()));
            throw new \InvalidArgumentException(
                sprintf(
                    'Категории с id: %d не существует.',
                    reset($diff)
                )
            );
        }

        $tableColumnAssociate = array(
            'announcement_category' => 'category_id', //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false, onDelete="CASCADE")  +
            'announcement_stats_element' => 'category_id', //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID")
            'attribute_category' => 'category_id', //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false) +
            'attribute_value_category' => 'category_id', //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false, onDelete="CASCADE") +
            'attribute_value_category_friend_category' => 'category_id', //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false, onDelete="CASCADE") +
            'callback' => 'Category_ID', //@ORM\JoinColumn(name="Category_ID", referencedColumnName="Message_ID", nullable=true)
            'brand_category' => 'category_id', //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", onDelete="CASCADE") +
            'manufacturer_category' => 'category_id', //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", onDelete="CASCADE") +
            'catalog_product' => 'category_id', //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false)
            'category_city_metadata' => 'category_id', //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false, onDelete="CASCADE") +
            'landing_page' => array('category_id', 'breadcrumb_category_id'), //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", onDelete="CASCADE") @ORM\JoinColumn(name="breadcrumb_category_id", referencedColumnName="Message_ID", onDelete="CASCADE") +
            'menu_item' => 'category_id', //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true, onDelete="CASCADE") +
            'Message157' => 'Parent_Razd', //@ORM\JoinColumn(name="Parent_Razd", referencedColumnName="Message_ID")
            'Message76' => 'cat_id', //@ORM\JoinColumn(name="cat_id", referencedColumnName="Message_ID", onDelete="CASCADE") +
            //'Message73' => 'cat_parent',
            'company_registration' => 'category_id', // @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true, onDelete="SET NULL") +
            'complaint' => 'Category_ID', //@ORM\JoinColumn(name="Category_ID", referencedColumnName="Message_ID", nullable=true)
            'parser_category_associate' => 'category_id', //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true, onDelete="SET NULL") +
            'demand' => 'category_id', //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true, onDelete="SET NULL") +
            'demand_category' => 'category_id', //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false, onDelete="CASCADE") +
            'demand_item' => 'category_id', // @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true, onDelete="SET NULL") +
            'demand_subscription_category' => 'category_id', //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false)
            'Message142' => array('Category_ID', 'P_Category_ID'), // @ORM\JoinColumn(name="P_Category_ID", referencedColumnName="Message_ID", nullable=true, onDelete="SET NULL") @ORM\JoinColumn(name="Category_ID", referencedColumnName="Message_ID", nullable=true, onDelete="SET NULL") +
            'Companies_images' => 'category_id', // @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true)
            'landing_template' => 'category_id', // @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true, onDelete="SET NULL") +
            'catalog_product_review' => 'Category_ID', // @ORM\JoinColumn(name="Category_ID", referencedColumnName="Message_ID", nullable=true)
            'company_review' => 'Category_ID', // @ORM\JoinColumn(name="Category_ID", referencedColumnName="Message_ID", nullable=true)
            'stats_category' => 'category_id', //TODO: Пересчет статистики?  @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true)
            'stats_element' => 'category_id', //TODO: Пересчет статистики?  @ORM\Column(type="integer", name="category_id", nullable=true)
            'source_point' => 'category_id', //@ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true)
        );

        $tableColumnAssociateToDelete = array(
            'category_extended' => 'category_id',
            'Category_friends' => 'Category_ID',
            'url_rewrite' => 'category_id',
            'category_test_item' => 'category',
            'attribute_category' => 'category_id',

            'announcement_stats_element' => 'category_id',
            'callback' => 'Category_ID',
            'catalog_product' => 'category_id',
            'Message157' => 'Parent_Razd',
            'complaint' => 'Category_ID',
            'demand_subscription_category' => 'category_id',
            'Companies_images' => 'category_id',
            'catalog_product_review' => 'Category_ID',
            'company_review' => 'Category_ID',

            'stats_category' => 'category_id',
            'stats_element' => 'category_id',
            'source_point' => 'category_id',
        );

        $conn->beginTransaction();

        foreach ($tableColumnAssociate as $table => $columns) {
            foreach ((array) $columns as $column) {
                $affectRows = $conn->executeUpdate(
                    sprintf(
                        'UPDATE IGNORE %s SET %s = :main_category WHERE %s = :additional_category',
                        $conn->quoteIdentifier($table),
                        $conn->quoteIdentifier($column),
                        $conn->quoteIdentifier($column)
                    ),
                    array(
                        'main_category' => $mainCategoryId,
                        'additional_category' => $additionalCategoryId,
                    )
                );

                $output->writeln(
                    sprintf(
                        '%s: Update %d rows, where table "%s" and column "%s"',
                        date('d.m.Y H:i:s'),
                        $affectRows,
                        $table,
                        $column
                    )
                );
            }
        }

        foreach ($tableColumnAssociateToDelete as $table => $column) {
            $conn->createQueryBuilder()
                ->delete(sprintf('%s', $conn->quoteIdentifier($table)))
                ->where(sprintf('%s = :additional_category', $conn->quoteIdentifier($column)))
                ->setParameter('additional_category', $additionalCategoryId)
                ->execute();

            $output->writeln(
                sprintf(
                    '%s: Delete additional category from table "%s"',
                    date('d.m.Y H:i:s'),
                    $table
                )
            );
        }

        $conn->commit();

        $archiveConn->beginTransaction();

        $output->writeln(sprintf('%s: Update archive stats_element', date('d.m.Y H:i:s')));
        $archiveConn->executeUpdate(
            'UPDATE IGNORE stats_element SET category_id = :main_category WHERE category_id = :additional_category',
            array('main_category' => $mainCategoryId, 'additional_category' => $additionalCategoryId)
        );
        $archiveConn->executeUpdate(
            'UPDATE IGNORE announcement_stats_element SET category_id = :main_category WHERE category_id = :additional_category',
            array('main_category' => $mainCategoryId, 'additional_category' => $additionalCategoryId)
        );

        $output->writeln(sprintf('%s: Delete archive stats_element', date('d.m.Y H:i:s')));
        $archiveConn->createQueryBuilder()
            ->delete('stats_element')
            ->where('category_id = :category_id')
            ->setParameter('category_id', $additionalCategoryId)
            ->execute();

        $archiveConn->createQueryBuilder()
            ->delete('announcement_stats_element')
            ->where('category_id = :category_id')
            ->setParameter('category_id', $additionalCategoryId)
            ->execute();

        $archiveConn->commit();

        $output->writeln(sprintf('%s: Delete category %d', date('d.m.Y H:i:s'), $additionalCategoryId));
        $conn->createQueryBuilder()
            ->delete('Message73')
            ->where('Message_ID = :category_id')
            ->setParameter('category_id', $additionalCategoryId)
            ->execute();

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }
}
