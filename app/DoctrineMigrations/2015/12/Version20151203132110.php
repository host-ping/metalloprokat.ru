<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151203132110 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Classificator_Regions ENGINE=InnoDB");
        $this->addSql("ALTER TABLE Classificator_Regions ADD CONSTRAINT FK_F02E1EA4F92F3E70 FOREIGN KEY (country_id) REFERENCES Classificator_Country (Country_ID)");
        $this->addSql("ALTER TABLE territorial_structure ADD CONSTRAINT FK_1A83148F98260155 FOREIGN KEY (region_id) REFERENCES Classificator_Regions (Regions_ID)");
        $this->addSql("ALTER TABLE territorial_structure ADD CONSTRAINT FK_1A83148F8BAC62AF FOREIGN KEY (city_id) REFERENCES Classificator_Region (Region_ID)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
