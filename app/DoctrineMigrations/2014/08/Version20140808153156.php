<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140808153156 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Message75 CHANGE is_slug_changed is_slug_changed DATETIME DEFAULT NULL');
        $this->addSql('UPDATE Message75 SET is_slug_changed = now() WHERE Message_ID = 2044039');
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
