<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160601162638 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
                    CREATE TABLE foursquare_place_by_coord_radius (
              id            INT AUTO_INCREMENT NOT NULL,
              foursquare_id VARCHAR(255)       NOT NULL,
              name          VARCHAR(255)       NOT NULL,
              coordinates   VARCHAR(255)       NOT NULL,
              radius        INT                NOT NULL,
              created_at    DATETIME           NOT NULL,
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
