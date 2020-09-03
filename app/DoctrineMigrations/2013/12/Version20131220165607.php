<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131220165607 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Message155 ADD slug VARCHAR(55) NOT NULL");
        $this->addSql('CREATE INDEX IDX_attr_slug ON Message155 (slug)');

    }

    public function down(Schema $schema)
    {
    }
}
