<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161018172931  extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


    public function up(Schema $schema)
    {
        $this->addSql('DELETE FROM stats_element WHERE company_id = 0');
        $this->addSql('DELETE FROM stats_product_change WHERE company_id = 0');
        $archiveConn = $this->container->get('doctrine.dbal.archive_connection');

        $archiveConn->executeUpdate(
            'DELETE FROM stats_element WHERE company_id = 0'
        );

        $archiveConn->executeUpdate(
            'DELETE FROM stats_product_change WHERE company_id = 0'
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
