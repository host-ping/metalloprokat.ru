<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\TerritorialBundle\Entity\Country;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150626130116 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE Message75 c SET c.country_id = :country_id WHERE c.country_id IS NOT NULL AND NOT EXISTS (SELECT cc.Country_ID FROM Classificator_Country cc WHERE cc.Country_ID = c.country_id)', array('country_id' => Country::COUNTRY_ID_RUSSIA));
        $this->addSql('ALTER TABLE Message75 ADD CONSTRAINT FK_8BE21763F92F3E70 FOREIGN KEY (country_id) REFERENCES Classificator_Country (Country_ID)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
