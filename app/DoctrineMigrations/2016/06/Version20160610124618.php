<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160610124618 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE instagram_account_tag_comment (
              id                       INT AUTO_INCREMENT       NOT NULL,
              instagram_account_tag_id INT DEFAULT NULL,
              text                     VARCHAR(5000) DEFAULT '' NOT NULL,
              enabled                  TINYINT(1) DEFAULT '1'   NOT NULL,
              created_at               DATETIME                 NOT NULL,
              INDEX IDX_3B95ACFFDFBC1E87 (instagram_account_tag_id),
              PRIMARY KEY (id)
            )
              DEFAULT CHARACTER SET utf8
              COLLATE utf8_unicode_ci
              ENGINE = InnoDB;
        ");
        
        $this->addSql("ALTER TABLE instagram_account_tag_comment ADD CONSTRAINT FK_3B95ACFFDFBC1E87 FOREIGN KEY (instagram_account_tag_id) REFERENCES instagram_account_tag (id);");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
