<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140902113435 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Message75 SET slug_changed_at = NULL WHERE slug_changed_at = '0000-00-00 00:00:00'");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
    }
}