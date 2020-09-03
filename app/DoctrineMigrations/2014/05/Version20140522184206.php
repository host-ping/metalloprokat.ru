<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140522184206 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('DELETE cc.* FROM Message76 cc JOIN Message73 c ON cc.cat_id = c.Message_ID WHERE c.allow_products = 0');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}