<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170214114355 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE demand ADD region_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_428D797398260155 ON demand (region_id)');
        $this->addSql('ALTER TABLE demand ADD CONSTRAINT FK_428D797398260155 FOREIGN KEY (region_id) REFERENCES Classificator_Regions (Regions_ID)');
        $this->addSql('
            UPDATE demand AS d
            JOIN Classificator_Region AS c ON c.Region_ID = d.city_id
            SET d.region_id = c.parent
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
