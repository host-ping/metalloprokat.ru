<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;


class Version20130910173306 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Message73 CHANGE COLUMN cat_parent cat_parent INT(11) DEFAULT NULL");
        $this->addSql("
            UPDATE Message73
            SET Section_ID = 0
            WHERE Section_ID > 0
                AND cat_parent > 0");

    }

    public function down(Schema $schema)
    {

    }
}
