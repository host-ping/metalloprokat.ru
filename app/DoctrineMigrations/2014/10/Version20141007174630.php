<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20141007174630 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE service_packages ADD processed_by INT DEFAULT NULL, ADD processed_at DATETIME DEFAULT NULL");
        $this->addSql("CREATE INDEX IDX_62C39B9A888A646A ON service_packages (processed_by)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
