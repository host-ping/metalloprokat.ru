<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170506092510 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE User
            ADD updated_at DATETIME DEFAULT NULL,
            ADD photo_name VARCHAR(255) DEFAULT NULL,
            ADD photo_original_name VARCHAR(255) DEFAULT NULL,
            ADD photo_mime_type VARCHAR(255) DEFAULT NULL,
            ADD photo_size INT DEFAULT NULL
        ');

        $this->addSql('
            UPDATE User u 
            JOIN Filetable f ON u.User_ID = f.Message_ID AND f.Field_ID = :discriminatorMap
            SET u.photo_name = f.Virt_Name, u.photo_original_name = f.Real_Name, u.photo_mime_type = f.File_Type, u.photo_size = f.File_Size
        ', array('discriminatorMap' => 411));

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
