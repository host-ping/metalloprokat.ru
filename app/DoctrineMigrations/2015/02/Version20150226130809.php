<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150226130809 extends AbstractMigration
{
    public function up(Schema $schema)
    {
       $this->addSql("ALTER TABLE announcement_zone ADD is_hidden TINYINT(1) DEFAULT '0' NOT NULL");
       $this->addSql("UPDATE announcement_zone SET is_hidden = true WHERE slug = 'add-on-layout'");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
