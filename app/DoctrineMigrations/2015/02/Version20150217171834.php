<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\TerritorialBundle\Entity\Country;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150217171834 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('
            UPDATE User u
            JOIN Classificator_Region city ON u.city_id = city.Region_ID AND city.country_id IS NOT NULL
            SET u.country_id = city.country_id
            WHERE u.country_id = 0'
        );
        $this->addSql('
            UPDATE User u
            JOIN Message75 c ON u.ConnectCompany = c.Message_ID AND c.country_id != 0
            SET u.country_id = c.country_id
            WHERE u.country_id = 0;
        ');
        $this->addSql('
            UPDATE User u
            SET u.country_id = :country_id
            WHERE u.country_id = 0;
        ', array('country_id' => Country::COUNTRY_ID_RUSSIA));

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
