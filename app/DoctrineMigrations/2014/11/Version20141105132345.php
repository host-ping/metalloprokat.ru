<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141105132345 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE announcement SET zone_id = 1 WHERE zone_id IS NULL;");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
