<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140711185953 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("RENAME TABLE demand_subscription TO metalspros_demand_subscription");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}