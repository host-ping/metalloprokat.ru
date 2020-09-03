<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150216143344 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('Дополнение к фоновому баннеру', 'add-on-layout', 842, 150, 0, 1, 19, 19);");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
