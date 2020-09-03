<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160504123500 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
                CREATE TABLE instagram_account_location (
              id                   INT AUTO_INCREMENT     NOT NULL,
              instagram_account_id INT      DEFAULT NULL,
              location_id          VARCHAR(255)           NOT NULL,
              enabled              TINYINT(1) DEFAULT '1' NOT NULL,
              last_parsed_at       DATETIME DEFAULT NULL,
              created_at           DATETIME DEFAULT NULL,
              INDEX IDX_5731CCB8A0E6FB4C (instagram_account_id),
              INDEX IDX_location_id (location_id),
              UNIQUE INDEX UNIQ_instagram_account_id_location_id (instagram_account_id, location_id),
              PRIMARY KEY (id)
            )
              DEFAULT CHARACTER SET utf8
              COLLATE utf8_unicode_ci
              ENGINE = InnoDB
        ");

        $this->addSql("
                ALTER TABLE instagram_account_location
          ADD CONSTRAINT FK_5731CCB8A0E6FB4C FOREIGN KEY (instagram_account_id) REFERENCES instagram_account (id)
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
