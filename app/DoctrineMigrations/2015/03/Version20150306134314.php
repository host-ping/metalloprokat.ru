<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150306134314 extends AbstractMigration
{
    public function up(Schema $schema)
    {
       $this->addSql(
           "
           CREATE TABLE company_product_attribute (
                  id  INT AUTO_INCREMENT NOT NULL,
                  company_id         INT                NOT NULL,
                  attribute_value_id INT DEFAULT NULL,
                  INDEX IDX_C847C88E979B1AD6 (company_id),
                  INDEX IDX_C847C88E65A22152 (attribute_value_id),
                  PRIMARY KEY (id)
                )
                  DEFAULT CHARACTER SET utf8
                  COLLATE utf8_unicode_ci
                  ENGINE = InnoDB"
       );

        $this->addSql(
            "ALTER TABLE company_product_attribute ADD CONSTRAINT FK_C847C88E979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID)"
        );

        $this->addSql(
            "ALTER TABLE company_product_attribute ADD CONSTRAINT FK_C847C88E65A22152 FOREIGN KEY (attribute_value_id) REFERENCES Message155 (Message_ID)"
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
