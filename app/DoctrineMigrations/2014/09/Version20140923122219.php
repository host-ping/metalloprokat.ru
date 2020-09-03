<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140923122219 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE redirect
        (
            id INT AUTO_INCREMENT NOT NULL,
            redirect_from VARCHAR(255) NOT NULL,
            redirect_to VARCHAR(255) NOT NULL,
            enabled TINYINT(1) DEFAULT '1' NOT NULL,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

        $this->addSql("INSERT INTO redirect
                        (
                            redirect_from,
                            redirect_to,
                            enabled,
                            created_at,
                            updated_at
                            )
                            VALUES
                            (
                            '/(\\/.+)?\\/sort\\/shpuntlarcena\\/(.+)?/ui',
                            '/$1/sort/spunt/spunt-larsena/$2',
                            1,
                            '2014-09-23 12:39:20',
                            '2014-09-23 12:39:21'
                        );");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
