<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131224115801 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE company_review ADD deleted_by INT DEFAULT NULL, ADD deleted_at DATETIME DEFAULT NULL;');
        $this->addSql('CREATE INDEX IDX_44DCE5B31F6FA0AF ON company_review (deleted_by);');
        $this->addSql('CREATE INDEX IDX_company_deleted_by ON company_review (company_id, deleted_by);');
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}