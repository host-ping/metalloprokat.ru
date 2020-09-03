<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131218113140 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE counter_category_country ADD min_price INT NOT NULL, ADD max_price INT DEFAULT '0' NOT NULL");
        $this->addSql("ALTER TABLE counter_country ADD min_price INT NOT NULL, ADD max_price INT DEFAULT '0' NOT NULL");
        $this->addSql("ALTER TABLE counter_city ADD min_price INT NOT NULL, ADD max_price INT DEFAULT '0' NOT NULL");
        $this->addSql("ALTER TABLE counter_category_city ADD min_price INT NOT NULL, ADD max_price INT DEFAULT '0' NOT NULL");
    }

    public function down(Schema $schema)
    {
    }
}
