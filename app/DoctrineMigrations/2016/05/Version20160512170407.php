<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160512170407 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE foursquare_place (
          id                INT AUTO_INCREMENT      NOT NULL,
          name              VARCHAR(255)            NOT NULL,
          instagram_added   TINYINT(1) DEFAULT '0'  NOT NULL,
          address           VARCHAR(255) DEFAULT '' NOT NULL,
          last_processed_at DATETIME                NOT NULL,
          PRIMARY KEY (id)
        )
          DEFAULT CHARACTER SET utf8
          COLLATE utf8_unicode_ci
          ENGINE = InnoDB;
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
