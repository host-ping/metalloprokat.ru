<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\ProjectBundle\Util\InsertUtil;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170315121706 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->skipIf(
            $this->container->getParameter('project.family') === 'metalloprokat',
            'Миграция не для металлопроката.'
        );

        $connections = array(
            'dafault' => $this->container->get('doctrine.dbal.default_connection'),
            'archive' => $this->container->get('doctrine.dbal.archive_connection')
        );
        /* @var $connections Connection[] */

        $productsIds = $this->connection->fetchAll(
            'SELECT product.Message_ID AS id FROM Message142 AS product WHERE product.is_virtual = true'
        );

        $productsIds = array_column($productsIds, 'id');

        foreach ($connections as $connection) {
            $callable = function (array $productsIds) use ($connection) {
                $connection->executeUpdate(
                    'DELETE spc FROM stats_product_change AS spc WHERE spc.product_id IN(:product_ids)',
                    array(
                        'product_ids' => $productsIds
                    ),
                    array(
                        'product_ids' => Connection::PARAM_INT_ARRAY
                    )
                );
            };

            InsertUtil::processBatch($productsIds, $callable, 1000);
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
