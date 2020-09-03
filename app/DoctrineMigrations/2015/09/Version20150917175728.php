<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150917175728 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE catalog_brand ADD title VARCHAR(100) NOT NULL, ADD slug VARCHAR(100) NOT NULL");
        $this->addSql("ALTER TABLE catalog_manufacturer ADD title VARCHAR(100) NOT NULL, ADD slug VARCHAR(100) NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
