<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\ProjectBundle\Doctrine\Utils;
use Metal\ProjectBundle\Util\InsertUtil;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170314143942 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $connections = array(
            'dafault' => $defaultConenction = $this->container->get('doctrine.dbal.default_connection'),
            'archive' => $this->container->get('doctrine.dbal.archive_connection')
        );
        /* @var $connections Connection[] */

        foreach ($connections as $connection) {
            Utils::checkConnection($connection);
            $companiesAndDate = $connection->fetchAll('SELECT
                  spc.company_id,
                  spc.date_created_at
                FROM stats_product_change AS spc
                WHERE spc.product_id = 0'
            );

            foreach ($companiesAndDate as $companyAndDate) {
                $this->write(sprintf(
                    '<info>%s</info>: Process date "<info>%s</info>" for companyId "<info>%d</info>"',
                    date('d.m.Y H:i:s'),
                    $companyAndDate['date_created_at'],
                    $companyAndDate['company_id']
                ));

                $dataToInsert = array();
                $skip = 0;
                $limit = 200;
                do {
                    Utils::checkConnection($defaultConenction);
                    $products = $defaultConenction->fetchAll(
                        'SELECT 
                             product.Created AS date_created_at,
                             product.Message_ID AS product_id,
                             product.Company_ID AS company_id
                         FROM Message142 AS product
                         WHERE product.Company_ID = :company_id
                         AND DATE(product.Created) = :created_at
                         AND product.is_virtual = false
                         LIMIT :skip, :limit
                     ',
                        array(
                            'company_id' => $companyAndDate['company_id'],
                            'created_at' => $companyAndDate['date_created_at'],
                            'skip' => $skip,
                            'limit' => $limit
                        ),
                        array(
                            'skip' => \PDO::PARAM_INT,
                            'limit' => \PDO::PARAM_INT
                        )
                    );

                    foreach ($products as $product) {
                        $dataToInsert[] = array(
                            'date_created_at' => $product['date_created_at'],
                            'product_id' => $product['product_id'],
                            'is_added' => 1,
                            'company_id' => $product['company_id'],
                        );
                    }

                    $skip += $limit;
                } while ($products);

                $this->write(sprintf(
                    '<info>%s</info>: Count "<info>%d</info>" insert data. ',
                    date('d.m.Y H:i:s'),
                    count($dataToInsert)
                ));

                if ($dataToInsert) {
                    Utils::checkConnection($connection);
                    InsertUtil::insertMultipleOrUpdate($connection, 'stats_product_change', $dataToInsert, array('is_added'), 100);
                }
            }
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
