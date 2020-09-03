<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131104171404 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE company_delivery_city
        ADD created_at DATETIME NOT NULL,
        ADD updated_at DATETIME NOT NULL,
        ADD phone VARCHAR(255) NOT NULL,
        ADD mail VARCHAR(255) NOT NULL,
        ADD site VARCHAR(255) DEFAULT '' NOT NULL,
        ADD adress VARCHAR(255) NOT NULL");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}