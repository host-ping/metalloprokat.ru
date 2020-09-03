<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160328140446 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE instagram_likers (
              id             INT AUTO_INCREMENT NOT NULL,
              user_id        VARCHAR(255)       NOT NULL,
              user_name      VARCHAR(255)       NOT NULL,
              user_full_name VARCHAR(255)       NOT NULL,
              created_at     DATETIME           NOT NULL,
              UNIQUE INDEX UNIQ_user_id (user_id),
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
