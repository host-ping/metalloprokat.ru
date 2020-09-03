<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\CategoriesBundle\Entity\ValueObject\LandingPageTerritoryProvider;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161205164304 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE landing_page_city_count (id INT AUTO_INCREMENT NOT NULL, landing_page_id INT NOT NULL, city_id INT DEFAULT NULL, results_count INT DEFAULT 0 NOT NULL, INDEX IDX_B511F12FDF122DC5 (landing_page_id), INDEX IDX_B511F12F8BAC62AF (city_id), UNIQUE INDEX UNIQ_landing_page_city (landing_page_id, city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE landing_page_city_count ADD CONSTRAINT FK_B511F12FDF122DC5 FOREIGN KEY (landing_page_id) REFERENCES landing_page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE landing_page_city_count ADD CONSTRAINT FK_B511F12F8BAC62AF FOREIGN KEY (city_id) REFERENCES Classificator_Region (Region_ID) ON DELETE SET NULL');
        $this->addSql("ALTER TABLE landing_page CHANGE category_id category_id INT DEFAULT NULL");
        $this->addSql('ALTER TABLE landing_page ADD landing_page_territory_id INT NOT NULL');


        $this->addSql('UPDATE landing_page SET landing_page_territory_id = :selected_territory',
            array('selected_territory' => LandingPageTerritoryProvider::SELECTED_TERRITORY)
        );
        $this->addSql('
            UPDATE landing_page 
            SET landing_page_territory_id = :all_countries 
            WHERE (city_id IS NULL AND region_id IS NULL AND country_id IS NULL)',
            array('all_countries' => LandingPageTerritoryProvider::ALL_COUNTRIES)
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
