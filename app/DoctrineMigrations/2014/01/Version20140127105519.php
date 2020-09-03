<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140127105519 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Classificator_Region ADD latitude NUMERIC(12, 8) DEFAULT NULL, ADD longitude NUMERIC(12, 8) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
    }
}
