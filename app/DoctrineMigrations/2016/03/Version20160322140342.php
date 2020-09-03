<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160322140342 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE demand AS d SET d.created_at = d.moderated_at WHERE d.created_at > '2016-01-20' AND DATE(d.created_at) = d.created_at");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
