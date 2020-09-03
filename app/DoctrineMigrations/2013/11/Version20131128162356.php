<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131128162356 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Message75 ADD latitude NUMERIC(10, 0) DEFAULT NULL, ADD longitude NUMERIC(10, 0) DEFAULT NULL, ADD coordinates_updated_at DATETIME DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
    }
}
