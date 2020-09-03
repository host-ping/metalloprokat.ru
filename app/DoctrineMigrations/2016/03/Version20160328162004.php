<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160328162004 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE catalog_product ADD is_title_non_unique TINYINT(1) DEFAULT '0' NOT NULL");
        
        $productsIds = $this->connection->fetchAll("
            SELECT p.id FROM catalog_product p
                WHERE p.title IN
                (SELECT cp.title FROM catalog_product cp GROUP BY cp.title HAVING COUNT(cp.title)>1)
        ");

        $ids = array();
        foreach ($productsIds as $id) {
            $ids[] = (int)$id['id'];
        }

        $this->addSql('UPDATE catalog_product p SET p.is_title_non_unique = 1 WHERE p.id IN (:ids)', array('ids' => $ids), array('ids' => Connection::PARAM_INT_ARRAY));

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
