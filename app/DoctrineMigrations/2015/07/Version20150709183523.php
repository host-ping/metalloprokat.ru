<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150709183523 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE demand ADD country_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_428D7973F92F3E70 ON demand (country_id)');
        $this->addSql('
            UPDATE demand d
            JOIN Classificator_Region city ON d.city_id = city.Region_ID
            SET d.country_id = city.country_id
        ');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
