<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140110171227 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE User CHANGE LastName LastName VARCHAR(255) DEFAULT NULL, CHANGE Job Job VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE Message75 CHANGE Manager Manager INT DEFAULT NULL');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}