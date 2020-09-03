<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140218123046 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE announcement_stats_element CHANGE COLUMN city_id city_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE announcement_stats_element ADD COLUMN category_id INT DEFAULT NULL");
    }

    public function down(Schema $schema)
    {

    }
}
