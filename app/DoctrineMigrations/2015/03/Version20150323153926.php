<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150323153926 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE announcement_zone SET width = 700, height = 40 WHERE slug = 'add-on-layout'");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
