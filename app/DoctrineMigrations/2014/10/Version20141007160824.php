<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20141007160824 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE announcement_order ADD processed_by INT DEFAULT NULL, ADD processed_at DATETIME DEFAULT NULL");
        $this->addSql("CREATE INDEX IDX_97D8BF4F888A646A ON announcement_order (processed_by)");
        $this->addSql("ALTER TABLE announcement_order ADD created_at DATETIME NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
