<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150710193552 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE callback ADD country_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_79F97426F92F3E70 ON callback (country_id)');
        $this->addSql('
            UPDATE callback c
            JOIN Classificator_Region city ON c.city_id = city.Region_ID
            SET c.country_id = city.country_id
        ');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
