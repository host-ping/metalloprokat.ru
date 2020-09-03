<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151221160753 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE content_entry
            ADD mime_type VARCHAR(15) DEFAULT NULL,
            ADD file_size INT DEFAULT NULL,
            ADD file_original_name VARCHAR(100) DEFAULT NULL,
            ADD file_name VARCHAR(255) DEFAULT NULL
        ');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
