<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141204153523 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE company_file (
            id INT AUTO_INCREMENT NOT NULL,
            company_id INT NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            mime_type VARCHAR(255) NOT NULL,
            file_size INT NOT NULL,
            file_path VARCHAR(255) NOT NULL,
            file_original_name VARCHAR(255) NOT NULL,
            PRIMARY KEY(id));');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
