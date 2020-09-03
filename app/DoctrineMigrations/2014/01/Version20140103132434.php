<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140103132434 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE User ADD COLUMN counter_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE user_counter CHANGE id id INT NOT NULL");
        $this->addSql('UPDATE user_counter SET id = user_id + 1000000');
        $this->addSql('UPDATE user_counter SET id = user_id');
        $this->addSql('UPDATE User SET counter_id = User_ID');
    }

    public function down(Schema $schema)
    {}
}
