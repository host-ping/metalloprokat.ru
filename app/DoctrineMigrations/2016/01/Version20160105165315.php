<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160105165315 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE normalized_company_url (
                id               INT AUTO_INCREMENT      NOT NULL,
                company_id       INT DEFAULT NULL,
                url_as_string VARCHAR(255) DEFAULT '' NOT NULL,
                INDEX IDX_2F093D02979B1AD6 (company_id),
                INDEX IDX_url_as_string (url_as_string),
                PRIMARY KEY (id)
              )
                DEFAULT CHARACTER SET utf8
                COLLATE utf8_unicode_ci
                ENGINE = InnoDB
        ");

        $this->addSql("
          ALTER TABLE normalized_company_url ADD CONSTRAINT FK_2F093D02979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID) ON DELETE CASCADE
        ");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
