<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\TerritorialBundle\Entity\Country;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160706153113 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Classificator_Region ADD display_in_country_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_2EE790C53C1F8682 ON Classificator_Region (display_in_country_id)');
        $this->addSql('UPDATE Classificator_Region SET display_in_country_id = country_id WHERE country_id IN (:enabled_countries_ids)',
            array('enabled_countries_ids' => Country::getEnabledCountriesIds()),
            array('enabled_countries_ids' => Connection::PARAM_INT_ARRAY)
        );
        $this->addSql('UPDATE Classificator_Region SET display_in_country_id = :_country_id WHERE country_id NOT IN (:enabled_countries_ids)',
            array('enabled_countries_ids' => Country::getEnabledCountriesIds(), '_country_id' => Country::COUNTRY_ID_RUSSIA),
            array('enabled_countries_ids' => Connection::PARAM_INT_ARRAY)
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
