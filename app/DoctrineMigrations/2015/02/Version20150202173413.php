<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150202173413 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('Почтовый 1', 'email-1', 672, 80, 45000, 4, 13, 13);
            INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('Почтовый 2', 'email-2', 672, 80, 35000, 4, 14, 14);
            INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('Почтовый 3', 'email-3', 672, 80, 25000, 4, 15, 15);
            INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('Почтовый 4', 'email-4', 672, 80, 15000, 4, 16, 16);
            INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('Почтовый 5', 'email-5', 672, 80, 5000, 4, 17, 17);
        ");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
