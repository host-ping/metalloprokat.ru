<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150730135541 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE agreement_template SET content = REPLACE(content, 'map-wrapper  is-bordered  float-right', 'map-wrapper is-bordered float-right js-map-wrapper')");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
