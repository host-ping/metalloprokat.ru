<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130805140201 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE demand SET source_type = 4 WHERE source_type = 1');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
