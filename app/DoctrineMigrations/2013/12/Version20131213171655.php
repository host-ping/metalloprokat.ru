<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131213171655 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Classificator_Country ADD title_locative VARCHAR(32) DEFAULT NULL, ADD title_genitive VARCHAR(32) DEFAULT NULL, ADD title_accusative VARCHAR(32) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
    }
}
