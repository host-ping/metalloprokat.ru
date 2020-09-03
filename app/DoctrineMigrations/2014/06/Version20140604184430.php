<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140604184430 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Classificator_Regions ADD title_locative VARCHAR(50) DEFAULT NULL, ADD title_genitive VARCHAR(50) DEFAULT NULL, ADD title_accusative VARCHAR(50) DEFAULT NULL');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
