<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140915201127 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE demand_subscription_territorial ADD territorial_structure_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_9966BAC2D6379C58 ON demand_subscription_territorial (territorial_structure_id)');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
