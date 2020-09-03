<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140603182226 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE INDEX IDX_deleted_at_ts ON Message75 (deleted_at_ts)');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}