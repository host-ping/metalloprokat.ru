<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140915115608 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('update User set job = null where job REGEXP "(^[^\w]$)|(^[-_*.]{2,})"');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
