<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140205133448 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            "RENAME TABLE company_payment_detail TO company_payment_details"
        );
    }

    public function down(Schema $schema)
    {
    }
}
