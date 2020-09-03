<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180821084532 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            <<<'SQL'
CREATE TABLE user_telegram (
  user_id             INT          NOT NULL,
  telegram_user_id    VARCHAR(255) NOT NULL,
  telegram_username   VARCHAR(255) DEFAULT NULL,
  telegram_first_name VARCHAR(255) DEFAULT NULL,
  telegram_last_name  VARCHAR(255) DEFAULT NULL,
  created_at          DATETIME     NOT NULL,
  UNIQUE INDEX UNIQ_E4BEC4DAFC28B263 (telegram_user_id),
  PRIMARY KEY (user_id)
)
  DEFAULT CHARACTER SET utf8
  COLLATE utf8_unicode_ci
  ENGINE = InnoDB
SQL
        );

        $this->addSql(
            <<<'SQL'
ALTER TABLE user_telegram
  ADD CONSTRAINT FK_E4BEC4DAA76ED395 FOREIGN KEY (user_id) REFERENCES User (User_ID)
SQL
        );
    }

    public FUNCTION down(SCHEMA $schema)
    {
    }
}
