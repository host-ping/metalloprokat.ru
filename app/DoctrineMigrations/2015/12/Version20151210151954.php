<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151210151954 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
              ALTER TABLE attribute
              ADD columns_count SMALLINT DEFAULT '1' NOT NULL,
              CHANGE url_priority url_priority SMALLINT DEFAULT '0' NOT NULL,
              CHANGE output_priority output_priority SMALLINT DEFAULT '0' NOT NULL
          ");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
