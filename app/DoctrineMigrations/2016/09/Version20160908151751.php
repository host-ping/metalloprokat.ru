<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160908151751 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
                    CREATE TABLE sphinx_search_log (
              id         INT AUTO_INCREMENT NOT NULL,
              created_at DATETIME           NOT NULL,
              raw_query  VARCHAR(5000) DEFAULT NULL,
              conn       INT                NOT NULL,
              time_real  NUMERIC(10, 3)     NOT NULL,
              time_wall  NUMERIC(10, 3)     NOT NULL,
              founds     INT                NOT NULL,
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
