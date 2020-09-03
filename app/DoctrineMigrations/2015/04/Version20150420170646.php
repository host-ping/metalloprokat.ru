<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150420170646 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE `User`
          ADD COLUMN display_only_in_specified_cities TINYINT(1) DEFAULT '0' NOT NULL,
          CHANGE visible_priority display_priority TINYINT(1) DEFAULT '1' NOT NULL
          ");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
