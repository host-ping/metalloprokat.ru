<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161118110947 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE grabber_proxy (
                  id          INT AUTO_INCREMENT NOT NULL,
                  proxy       VARCHAR(255)       NOT NULL,
                  created_at  DATETIME           NOT NULL,
                  updated_at  DATETIME DEFAULT NULL,
                  disabled_at DATETIME DEFAULT NULL,
                  PRIMARY KEY (id)
                )
                  DEFAULT CHARACTER SET utf8
                  COLLATE utf8_unicode_ci
                  ENGINE = InnoDB;
                ");

        $this->addSql("CREATE UNIQUE INDEX UNIQ_proxy ON grabber_proxy (proxy);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
