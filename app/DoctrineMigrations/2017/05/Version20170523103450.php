<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170523103450 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            ALTER TABLE company_payment_details 
            CHANGE attachment file_name VARCHAR(255) DEFAULT NULL, 
            CHANGE attachment_file_name file_original_name VARCHAR(255) DEFAULT NULL, 
            ADD file_mime_type VARCHAR(255) DEFAULT NULL, 
            ADD file_size INT DEFAULT NULL        
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
