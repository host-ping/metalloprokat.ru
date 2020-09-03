<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170214123600 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE callback ADD region_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_79F9742698260155 ON callback (region_id)');
        $this->addSql('ALTER TABLE callback ADD CONSTRAINT FK_79F9742698260155 FOREIGN KEY (region_id) REFERENCES Classificator_Regions (Regions_ID)');

        $this->addSql('
            UPDATE callback AS cal
            JOIN Classificator_Region AS c ON c.Region_ID = cal.city_id
            SET cal.region_id = c.parent
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
