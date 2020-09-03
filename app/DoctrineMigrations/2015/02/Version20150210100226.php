<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150210100226 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Message73 SET regularName = '' WHERE regularName IS NULL");
        $this->addSql("ALTER TABLE Message73 ADD extended_pattern VARCHAR(1000) NOT NULL, CHANGE regularName pattern VARCHAR(1000) NOT NULL");
        $this->addSql("UPDATE Message73 SET Descr = '' WHERE Descr IS NULL");
        $this->addSql("ALTER TABLE Message73 CHANGE Descr description VARCHAR(2000) NOT NULL, DROP Keywords");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
