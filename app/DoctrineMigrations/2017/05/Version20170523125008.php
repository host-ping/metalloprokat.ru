<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170523125008 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            ALTER TABLE company_registration 
            CHANGE price_path price_name VARCHAR(255) DEFAULT NULL, 
            ADD price_mime_type VARCHAR(255) DEFAULT NULL, 
            ADD price_size INT DEFAULT NULL, 
            CHANGE price_original_name price_original_name VARCHAR(255) DEFAULT NULL
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
