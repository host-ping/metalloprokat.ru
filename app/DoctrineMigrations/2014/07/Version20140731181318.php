<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140731181318 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE announcement_stats_element e
            JOIN announcement a ON e.announcement_id = a.id
             SET e.source_type_id = 5
             WHERE a.zone_type IN (2,3,4,5,6,7,8) AND e.source_type_id = 2
        ");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}