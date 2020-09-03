<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131107160907 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE company_counter ADD new_complaints_count INT NOT NULL, ADD new_demands_count INT NOT NULL, ADD new_company_reviews_count INT NOT NULL, ADD new_callbacks_count INT NOT NULL");
        $this->addSql("ALTER TABLE user_counter ADD new_moderator_answers INT NOT NULL");
    }

    public function down(Schema $schema)
    {
    }
}
