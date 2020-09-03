<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131111112315 extends AbstractMigration
{
    public function up(Schema $schema)
    {

        $this->addSql("ALTER TABLE Message75 ADD slug VARCHAR(50) DEFAULT NULL");

    }

    public function down(Schema $schema)
    {

    }
}
