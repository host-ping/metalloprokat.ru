<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150213095846 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('Фоновый', 'background', 0, 0, 30000, 1, 18, 18);");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
