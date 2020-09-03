<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160419110508 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
                CREATE TABLE parser_company_to_category (
              id                 INT AUTO_INCREMENT     NOT NULL,
              company_id         INT DEFAULT NULL,
              parsed_category_id INT                    NOT NULL,
              matched            TINYINT(1) DEFAULT '0' NOT NULL,
              INDEX IDX_8AE7DA21979B1AD6 (company_id),
              INDEX IDX_8AE7DA21E4CD5918 (parsed_category_id),
              PRIMARY KEY (id)
            )
              DEFAULT CHARACTER SET utf8
              COLLATE utf8_unicode_ci
              ENGINE = InnoDB;
        ");

        $this->addSql("
            ALTER TABLE parser_company_to_category
          ADD CONSTRAINT FK_8AE7DA21979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID);
        ");

        $this->addSql("
            ALTER TABLE parser_company_to_category
          ADD CONSTRAINT FK_8AE7DA21E4CD5918 FOREIGN KEY (parsed_category_id) REFERENCES parser_category_associate (parser_category_id)
          ON DELETE CASCADE;
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
