<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130513133918 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE demand CHANGE COLUMN body body TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE demand_item CHANGE COLUMN title title VARCHAR(255) NOT NULL');

    }

    public function down(Schema $schema)
    {


    }
}
