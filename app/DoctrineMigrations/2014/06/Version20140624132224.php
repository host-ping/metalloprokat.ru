<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140624132224 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Message75 MODIFY LastUpdated TIMESTAMP');
        $this->addSql('UPDATE IGNORE company_counter AS cc SET cc.products_updated_at = (
  SELECT p.LastUpdated FROM Message142 AS p WHERE p.Company_ID = cc.company_id ORDER BY p.LastUpdated DESC LIMIT 1
)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
