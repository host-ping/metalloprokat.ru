<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131212141122 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Classificator_Region ADD COLUMN country_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE Classificator_Regions ADD COLUMN country_id INT DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
    }
}
