<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131115140802 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE company_attribute ADD type SMALLINT NOT NULL, DROP has_production, DROP has_cutting, DROP has_bending, DROP has_delivery");
    }

    public function down(Schema $schema)
    {
    }
}
