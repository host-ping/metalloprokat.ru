<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131204183207 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE category_counter ADD min_price INT NOT NULL, ADD max_price INT NOT NULL");
    }

    public function down(Schema $schema)
    {
    }
}
