<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131105175154 extends AbstractMigration
{
    public function up(Schema $schema)
    {
       $this->addSql('ALTER TABLE complaint ADD viewed_by INT DEFAULT NULL, ADD viewed_at DATETIME DEFAULT NULL;');
       $this->addSql('CREATE INDEX IDX_5F2732B599D97A03 ON complaint (viewed_by);');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
