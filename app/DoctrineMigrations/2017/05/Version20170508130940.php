<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170508130940 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            ALTER TABLE client_review 
            ADD updated_at DATETIME DEFAULT NULL, 
            CHANGE photo photo_name VARCHAR(255) DEFAULT NULL, 
            CHANGE photo_mime photo_mime_type VARCHAR(255) DEFAULT NULL, 
            ADD photo_size INT DEFAULT NULL,
            CHANGE photo_original_name photo_original_name VARCHAR(255) DEFAULT NULL;
        
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
