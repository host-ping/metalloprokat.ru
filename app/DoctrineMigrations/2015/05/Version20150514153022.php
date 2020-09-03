<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150514153022 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE email_deferred (
  id           INT AUTO_INCREMENT     NOT NULL,
  email        VARCHAR(255)           NOT NULL,
  log          VARCHAR(1000)          NOT NULL,
  date_send    DATE                   NOT NULL,
  unsubscribed TINYINT(1) DEFAULT '0' NOT NULL,
  INDEX IDX_email (email),
  INDEX IDX_date (date_send),
  PRIMARY KEY (id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB;
        ");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
