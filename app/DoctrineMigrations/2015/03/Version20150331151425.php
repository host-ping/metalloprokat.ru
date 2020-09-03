<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150331151425 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE demand ADD fake_updated_at DATETIME NOT NULL");
        $this->addSql("UPDATE demand SET updated_at = created_at WHERE updated_at IS NULL OR updated_at = '0000-00-00'");
        $this->addSql("UPDATE demand SET fake_updated_at = '0000-00-00'");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
