<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140709131325 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs

        /* Create tables */
        $this->addSql("
            CREATE TABLE CategoryTestItem (
              id INT NOT NULL,
              title VARCHAR(250) NOT NULL DEFAULT '',
              category INT NOT NULL,
              PRIMARY KEY (id)
            )
              DEFAULT CHARACTER SET utf8
              COLLATE utf8_unicode_ci
              ENGINE = InnoDB
        ");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}