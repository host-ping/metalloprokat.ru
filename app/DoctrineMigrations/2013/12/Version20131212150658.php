<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131212150658 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Message75 ADD country_id INT NOT NULL DEFAULT 165");
        $this->addSql("ALTER TABLE Classificator_Country ADD base_host VARCHAR(50) NOT NULL");
    }

    public function down(Schema $schema)
    {
    }
}
