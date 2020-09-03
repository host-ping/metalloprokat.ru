<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170526103908 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            ALTER TABLE instagram_photo
            CHANGE file_name image_name VARCHAR(255) DEFAULT NULL,
            CHANGE mime_type image_mime_type VARCHAR(255) DEFAULT NULL,
            CHANGE created_at created_at DATETIME DEFAULT NULL,
            CHANGE file_size image_size INT DEFAULT NULL,
            ADD image_original_name VARCHAR(255) DEFAULT NULL
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
