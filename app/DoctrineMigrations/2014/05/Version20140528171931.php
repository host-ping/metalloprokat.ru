<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140528171931 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("DROP INDEX UNIQ_demand_category ON demand_view");
    }

    public function down(Schema $schema)
    {
    }
}
