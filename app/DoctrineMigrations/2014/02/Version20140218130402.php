<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140218130402 extends AbstractMigration
{
    public function up(Schema $schema)
    {

        $this->addSql('ALTER TABLE User CHANGE phone phone VARCHAR(120) DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
