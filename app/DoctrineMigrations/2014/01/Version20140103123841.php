<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140103123841 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE company_counter CHANGE id id INT NOT NULL");
        $this->addSql('UPDATE company_counter SET id = company_id + 1000000');
        $this->addSql('UPDATE company_counter SET id = company_id');
        $this->addSql('ALTER TABLE Message75 ADD COLUMN counter_id INT DEFAULT NULL');
        $this->addSql('UPDATE Message75 SET counter_id = Message_ID');
    }

    public function down(Schema $schema)
    {
    }
}
