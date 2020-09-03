<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150828154953 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE catalog_brand
                        ADD mime_type VARCHAR(15) DEFAULT NULL,
                        ADD file_size INT DEFAULT NULL,
                        ADD file_path VARCHAR(255) DEFAULT NULL,
                        ADD file_original_name VARCHAR(100) DEFAULT NULL;");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
