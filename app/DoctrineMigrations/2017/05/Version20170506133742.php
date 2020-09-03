<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170506133742 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Companies_images 
            CHANGE file_original_name photo_original_name VARCHAR(255) DEFAULT NULL, 
            CHANGE mime_type photo_mime_type VARCHAR(255) DEFAULT NULL,
            CHANGE name photo_name VARCHAR(255) DEFAULT NULL, 
            CHANGE file_size photo_size INT DEFAULT NULL;
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
