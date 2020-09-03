<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170315124813 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $connections = array(
            'dafault' => $this->container->get('doctrine.dbal.default_connection'),
            'archive' => $this->container->get('doctrine.dbal.archive_connection')
        );
        /* @var $connections Connection[] */

        foreach ($connections as $connection) {
            $connection->executeUpdate(
                'DELETE FROM stats_product_change WHERE product_id = 0'
            );
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
