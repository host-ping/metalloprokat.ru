<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140414152132 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE demand CHANGE city_id city_id INT DEFAULT NULL');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
