<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130905115237 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Message73 ADD title_locative VARCHAR(255) DEFAULT NULL, ADD title_genitive VARCHAR(255) DEFAULT NULL, ADD title_accusative VARCHAR(255) DEFAULT NULL, ADD title_prepositional VARCHAR(255) DEFAULT NULL');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
