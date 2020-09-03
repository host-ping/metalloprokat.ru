<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170517112424 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            'DELETE FROM demand_category WHERE demand_category.demand_id IN (
                SELECT t.demand_id FROM (
                    SELECT d_c.demand_id FROM demand_category d_c LEFT JOIN demand d ON d.id = d_c.demand_id WHERE d.id IS NULL
                ) AS t
            )
        '
        );

        $this->addSql(
            'DELETE FROM demand_item WHERE demand_item.demand_id IN (
                SELECT t.demand_id FROM (
                    SELECT d_i.demand_id FROM demand_item d_i LEFT JOIN demand d ON d.id = d_i.demand_id WHERE d.id IS NULL
                ) AS t
            )
        '
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
