<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160919160604 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {


        // Киргизия
        $this->addSql('UPDATE Classificator_Region SET country_id = 92, display_in_country_id = 165 WHERE Region_ID = 2356');
        $this->addSql("UPDATE Classificator_Region SET country_id = 92, display_in_country_id = 165, Keyword = 'osh' WHERE Region_ID = 2380");
        // Области проставляем тоже страну
        $this->addSql('UPDATE Classificator_Regions SET country_id = 92 WHERE Regions_ID = 100');
        // США
        $this->addSql("UPDATE Classificator_Region SET country_id = 189, display_in_country_id = 165, Keyword = 'new-york' WHERE Region_ID = 2416");
        // Области проставляем тоже страну
        $this->addSql('UPDATE Classificator_Regions SET country_id = 189 WHERE Regions_ID = 120');

        // Ураярви наверное это Alajärvi
        $this->addSql("UPDATE Classificator_Region SET country_id = 217, display_in_country_id = 165, Keyword = 'alajarvi' WHERE Region_ID = 2409");
        // Будапешт
        $this->addSql("UPDATE Classificator_Region SET country_id = 37, display_in_country_id = 165, Keyword = 'budapest' WHERE Region_ID = 2410");
        // Велико Тырново
        $this->addSql("UPDATE Classificator_Region SET country_id = 25, display_in_country_id = 165, Keyword = 'veliko-tarnovo' WHERE Region_ID = 2412");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
