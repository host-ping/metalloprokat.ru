<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131129163836 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE company_delivery_city ADD address_new VARCHAR(100) NOT NULL, ADD coordinates_updated_at DATETIME DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
    }
}
