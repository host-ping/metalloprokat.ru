<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150416184351 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE demand CHANGE fake_updated_at fake_updated_at DATETIME DEFAULT NULL');
        $this->addSql("UPDATE demand SET fake_updated_at = NULL WHERE fake_updated_at = '0000-00-00'");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
