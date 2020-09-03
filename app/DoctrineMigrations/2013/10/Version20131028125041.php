<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131028125041 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE company_review DROP INDEX IDX_4370E085AA334807, ADD UNIQUE INDEX UNIQ_44DCE5B3AA334807 (answer_id)");
        $this->addSql("ALTER TABLE company_review CHANGE type type TINYINT(1) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
    }
}
