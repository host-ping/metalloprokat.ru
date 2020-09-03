<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150218122120 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
                        UPDATE demand AS d SET d.category_id = null
                        WHERE NOT EXISTS (
                            SELECT *
                            FROM Message73 AS category
                            WHERE d.category_id = category.Message_ID
                        )
                              AND d.category_id IS NOT NULL
        ");

        $this->addSql("
                        UPDATE demand_item AS di SET di.category_id = null
                        WHERE NOT EXISTS (
                            SELECT *
                            FROM Message73 AS category
                            WHERE di.category_id = category.Message_ID
                        )
                              AND di.category_id IS NOT NULL
        ");

        $this->addSql("ALTER TABLE demand ADD CONSTRAINT FK_428D797312469DE2 FOREIGN KEY (category_id) REFERENCES Message73 (Message_ID) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE demand_item ADD CONSTRAINT FK_C1D9855812469DE2 FOREIGN KEY (category_id) REFERENCES Message73 (Message_ID) ON DELETE SET NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
