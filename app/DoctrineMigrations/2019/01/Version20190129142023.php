<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190129142023 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("
            CREATE TABLE IF NOT EXISTS announcement_territorial (
              id              INT AUTO_INCREMENT NOT NULL,
              announcement_id INT                NOT NULL,
              country_id      INT DEFAULT NULL,
              region_id       INT DEFAULT NULL,
              city_id         INT DEFAULT NULL,
              INDEX IDX_773CEB59913AEA17 (announcement_id),
              INDEX IDX_773CEB59F92F3E70 (country_id),
              INDEX IDX_773CEB5998260155 (region_id),
              INDEX IDX_773CEB598BAC62AF (city_id),
              UNIQUE INDEX UNIQ_announcement_county (country_id, announcement_id),
              UNIQUE INDEX UNIQ_announcement_region (region_id, announcement_id),
              UNIQUE INDEX UNIQ_announcement_city (city_id, announcement_id),
              PRIMARY KEY (id)
            )
              DEFAULT CHARACTER SET utf8
              COLLATE utf8_unicode_ci
              ENGINE = InnoDB
        ");


    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
