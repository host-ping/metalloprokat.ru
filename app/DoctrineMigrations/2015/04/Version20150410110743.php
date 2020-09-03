<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150410110743 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE company_registration SET updater = 1 WHERE updater IS NULL");
        $this->addSql("ALTER TABLE company_registration CHANGE updater updater SMALLINT DEFAULT '1' NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
