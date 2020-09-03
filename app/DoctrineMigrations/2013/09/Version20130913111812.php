<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

class Version20130913111812 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Message73 ADD COLUMN branch_ids VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE Message73 ADD COLUMN slug_combined VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE Message73 ADD COLUMN slug_combined_spros VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema)
    {

    }
}
