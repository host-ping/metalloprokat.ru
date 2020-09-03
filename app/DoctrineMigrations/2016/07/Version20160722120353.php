<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160722120353 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            CREATE TABLE client_review (
                id INT AUTO_INCREMENT NOT NULL, 
                deleted_by INT DEFAULT NULL, 
                moderated_by INT DEFAULT NULL, 
                company_id INT DEFAULT NULL, 
                title VARCHAR(255) DEFAULT NULL, 
                created_at DATETIME NOT NULL, 
                comment VARCHAR(255) NOT NULL, 
                name VARCHAR(255) DEFAULT NULL, 
                deleted_at DATETIME DEFAULT NULL, 
                moderated_at DATETIME DEFAULT NULL, 
                logo VARCHAR(255) NOT NULL, 
                logo_mime VARCHAR(15) DEFAULT NULL, 
                logo_original_name VARCHAR(100) DEFAULT NULL, 
                INDEX IDX_2CE5CA751F6FA0AF (deleted_by), 
                INDEX IDX_2CE5CA756F9F06A4 (moderated_by), 
                INDEX IDX_2CE5CA75979B1AD6 (company_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
