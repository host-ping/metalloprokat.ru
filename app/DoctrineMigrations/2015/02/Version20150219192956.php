<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150219192956 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('
            UPDATE announcement_zone_status zs JOIN announcement a ON zs.announcement_id = a.id
            SET zs.starts_at = a.starts_at, zs.ends_at = a.ends_at
        ');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
