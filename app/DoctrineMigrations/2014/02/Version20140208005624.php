<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140208005624 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE announcement (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, starts_at DATETIME NOT NULL, ends_at DATETIME NOT NULL, is_payed TINYINT(1) NOT NULL, link VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, file_size INT NOT NULL, file_path VARCHAR(255) NOT NULL, file_original_name VARCHAR(255) NOT NULL, INDEX IDX_4DB9D91C979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
    }

    public function down(Schema $schema)
    {

    }
}
