<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160704145918 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE instagram_account_events (
              id                   INT AUTO_INCREMENT NOT NULL,
              instagram_account_id INT          DEFAULT NULL,
              instagram_event_id   VARCHAR(255)       NOT NULL,
              event_type           INT                NOT NULL,
              media_id             VARCHAR(255) DEFAULT NULL,
              event_user_name      VARCHAR(255)       NOT NULL,
              event_user_id        VARCHAR(255)       NOT NULL,
              event_message        VARCHAR(500)       NOT NULL,
              event_date           DATETIME           NOT NULL,
              INDEX IDX_36E92ADCA0E6FB4C (instagram_account_id),
              PRIMARY KEY (id)
            )
              DEFAULT CHARACTER SET utf8
              COLLATE utf8_unicode_ci
              ENGINE = InnoDB
        ");

        $this->addSql("
            ALTER TABLE instagram_account_events
          ADD CONSTRAINT FK_36E92ADCA0E6FB4C FOREIGN KEY (instagram_account_id) REFERENCES instagram_account (id);        
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
