<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131129160105 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE company_delivery_city ADD latitude NUMERIC(12, 8) DEFAULT NULL, ADD longitude NUMERIC(12, 8) DEFAULT NULL");
   }

    public function down(Schema $schema)
    {
    }
}
