<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170504091701 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Message75
            ADD company_logo_size INT DEFAULT NULL,
            CHANGE company_logo company_logo_name VARCHAR(255) DEFAULT NULL,
            CHANGE company_logo_mime company_logo_mime_type VARCHAR(255) DEFAULT NULL,
            CHANGE company_logo_original_name company_logo_original_name VARCHAR(255) DEFAULT NULL

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
