<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150422175524 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Classificator_Regions AS r JOIN Classificator_Country AS c
            ON REPLACE(r.Regions_Name, char(10), '') = c.Country_Name
            SET r.country_id = c.Country_ID
            WHERE r.country_id IS NULL
        ");

        $this->addSql("UPDATE Classificator_Region AS c JOIN Classificator_Regions AS r
            ON c.parent = r.Regions_ID
            SET c.country_id = r.country_id
            WHERE c.country_id IS NULL
        ");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
