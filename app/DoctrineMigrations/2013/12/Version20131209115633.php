<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131209115633 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Message142 ADD is_contract_price TINYINT(1) DEFAULT '0' NOT NULL");
    }

    public function down(Schema $schema)
    {
    }

}
