<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150515123132 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Classificator_Regions SET Regions_Name = trim(Regions_Name)");
        $this->addSql("UPDATE Classificator_Regions SET Regions_Name = replace(Regions_Name, char(9), '')");
        $this->addSql("UPDATE Classificator_Regions SET Regions_Name = replace(Regions_Name, char(10), '')");

        $this->addSql("UPDATE Classificator_Regions r JOIN Classificator_Country c
            ON r.Regions_Name = c.Country_Name AND r.country_id IS NULL
            SET r.country_id = c.Country_ID
        ");
        $this->addSql("UPDATE Classificator_Region city JOIN Classificator_Regions r
            ON city.parent = r.Regions_ID AND city.country_id IS NULL
            SET city.country_id = r.country_id
        ");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
