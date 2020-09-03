<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140113172951 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE stats_day ADD `year_month` SMALLINT DEFAULT '0' NOT NULL, ADD year_week SMALLINT DEFAULT '0' NOT NULL");
        $this->addSql("ALTER TABLE stats_daily DROP `year_month`, DROP year_week");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
