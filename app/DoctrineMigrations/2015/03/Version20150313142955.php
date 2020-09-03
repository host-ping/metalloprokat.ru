<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150313142955 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            "CREATE TABLE company_registration (
                  id                  INT AUTO_INCREMENT     NOT NULL,
                  company_id          INT      DEFAULT NULL,
                  price_path          VARCHAR(255)           NOT NULL,
                  price_original_name VARCHAR(255)           NOT NULL,
                  attributes          LONGTEXT DEFAULT NULL
                  COMMENT '(DC2Type:json_array)',
                  is_second_step_done TINYINT(1) DEFAULT '0' NOT NULL,
                  is_third_step_done  TINYINT(1) DEFAULT '0' NOT NULL,
                  created_at          DATETIME               NOT NULL,
                  updated_at          DATETIME               NOT NULL,
                  UNIQUE INDEX UNIQ_9C656D69979B1AD6 (company_id),
                  PRIMARY KEY (id)
                )
                  DEFAULT CHARACTER SET utf8
                  COLLATE utf8_unicode_ci
                  ENGINE = InnoDB"
        );

        $this->addSql(
            "ALTER TABLE company_registration ADD CONSTRAINT FK_9C656D69979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID) ON DELETE SET NULL"
        );

        $this->addSql("ALTER TABLE company_registration DROP FOREIGN KEY FK_9C656D69979B1AD6");
        $this->addSql("ALTER TABLE company_registration ADD category_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE company_registration ADD CONSTRAINT FK_9C656D6912469DE2 FOREIGN KEY (category_id) REFERENCES Message73 (Message_ID) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE company_registration ADD CONSTRAINT FK_9C656D69979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID)");
        $this->addSql("CREATE INDEX IDX_9C656D6912469DE2 ON company_registration (category_id)");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
