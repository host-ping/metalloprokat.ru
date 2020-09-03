<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140529160212 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE demand_item SET volume_type = 6 WHERE volume_type = 3');
        $this->addSql('update demand_item set volume_type = 3 where volume_type = 2');
        $this->addSql('UPDATE demand_item SET volume_type = 9 WHERE volume_type = 4');
        $this->addSql('UPDATE demand_item SET volume_type = 11 WHERE volume_type = 5');
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
