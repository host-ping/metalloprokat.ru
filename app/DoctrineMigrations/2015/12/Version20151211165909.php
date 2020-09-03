<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151211165909 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
                CREATE TABLE normalized_email (
                  id        INT AUTO_INCREMENT      NOT NULL,
                  user_id   INT DEFAULT NULL,
                  demand_id INT DEFAULT NULL,
                  email     VARCHAR(255) DEFAULT '' NOT NULL,
                  INDEX IDX_3BCCCEC4A76ED395 (user_id),
                  INDEX IDX_3BCCCEC45D022E59 (demand_id),
                  INDEX IDX_email (email),
                  PRIMARY KEY (id)
                )
                  DEFAULT CHARACTER SET utf8
                  COLLATE utf8_unicode_ci
                  ENGINE = InnoDB
        ");

        $this->addSql("
                ALTER TABLE normalized_email ADD CONSTRAINT FK_3BCCCEC4A76ED395 FOREIGN KEY (user_id) REFERENCES User (User_ID)
                  ON DELETE CASCADE
        ");

        $this->addSql("
                ALTER TABLE normalized_email ADD CONSTRAINT FK_3BCCCEC45D022E59 FOREIGN KEY (demand_id) REFERENCES demand (id)
                  ON DELETE CASCADE
        ");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
