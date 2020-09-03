<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160418102222 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
                    CREATE TABLE user_registration_with_parser (
              user_id    INT         NOT NULL,
              site_code  VARCHAR(50) NOT NULL,
              created_at DATETIME    NOT NULL,
              PRIMARY KEY (user_id)
            )
              DEFAULT CHARACTER SET utf8
              COLLATE utf8_unicode_ci
              ENGINE = InnoDB;
        ");

        $this->addSql("
          ALTER TABLE user_registration_with_parser ADD CONSTRAINT FK_2A9A1811A76ED395 FOREIGN KEY (user_id) REFERENCES User (User_ID);
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
