<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130724123711 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE demand CHANGE moderated_at moderated_at DATETIME DEFAULT NULL');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
