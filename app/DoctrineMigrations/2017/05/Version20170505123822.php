<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170505123822 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $this->addSql('
            ALTER TABLE catalog_product 
            CHANGE file_original_name photo_original_name VARCHAR(255) DEFAULT NULL, 
            CHANGE mime_type photo_mime_type VARCHAR(255) DEFAULT NULL,  
            CHANGE file_path photo_name VARCHAR(255) DEFAULT NULL, 
            CHANGE file_size photo_size INT DEFAULT NULL
        
        ');
        $this->addSql('
            ALTER TABLE catalog_brand 
            ADD updated_at DATETIME DEFAULT NULL, 
            CHANGE file_original_name logo_original_name VARCHAR(255) DEFAULT NULL, 
            CHANGE mime_type logo_mime_type VARCHAR(255) DEFAULT NULL, 
            CHANGE file_path logo_name VARCHAR(255) DEFAULT NULL, 
            CHANGE file_size logo_size INT DEFAULT NULL
        ');
        $this->addSql('
            ALTER TABLE catalog_manufacturer 
            ADD updated_at DATETIME DEFAULT NULL, 
            CHANGE file_original_name logo_original_name VARCHAR(255) DEFAULT NULL, 
            CHANGE mime_type logo_mime_type VARCHAR(255),  
            CHANGE file_path logo_name VARCHAR(255) DEFAULT NULL, 
            CHANGE file_size logo_size INT DEFAULT NULL
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
