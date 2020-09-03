<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131213145230 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Classificator_Region ADD INDEX IDX_country_id (country_id)');
        $this->addSql('ALTER TABLE Classificator_Regions ADD INDEX IDX_country_id (country_id)');
    }

    public function down(Schema $schema)
    {

    }
}
