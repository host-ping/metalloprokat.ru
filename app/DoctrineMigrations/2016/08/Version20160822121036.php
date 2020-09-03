<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160822121036 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE landing_page ADD CONSTRAINT FK_87A7C8998BAC62AF FOREIGN KEY (city_id) REFERENCES Classificator_Region (Region_ID);');
        $this->addSql('ALTER TABLE landing_page ADD CONSTRAINT FK_87A7C89998260155 FOREIGN KEY (region_id) REFERENCES Classificator_Regions (Regions_ID);');
        $this->addSql('ALTER TABLE landing_page ADD CONSTRAINT FK_87A7C899F92F3E70 FOREIGN KEY (country_id) REFERENCES Classificator_Country (Country_ID);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
