<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140227151740 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE agreement_template SET title = 'Договор' WHERE id = 1");
        $this->addSql("UPDATE agreement_template SET title = 'Лицензионное соглашение' WHERE id = 2");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
