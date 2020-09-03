<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170519132355 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            ALTER TABLE landing_template 
            CHANGE file_path file_name VARCHAR(255) DEFAULT NULL, 
            CHANGE mime_type file_mime_type VARCHAR(255) DEFAULT NULL, 
            CHANGE file_size file_size INT DEFAULT NULL, 
            CHANGE file_original_name file_original_name VARCHAR(255) DEFAULT NULL;
            '
        );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
