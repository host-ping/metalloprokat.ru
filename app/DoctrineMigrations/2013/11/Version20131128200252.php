<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131128200252 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Message75 CHANGE latitude latitude NUMERIC(12, 8) DEFAULT NULL, CHANGE longitude longitude NUMERIC(12, 8) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
    }
}
