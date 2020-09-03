<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150216122847 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Message142 SET Checked = 2 WHERE Checked = 0 AND branch_office_id IS NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
