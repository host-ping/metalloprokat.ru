<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140603130708 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE company_minisite SET background_color = '#cce6d7' where background_color = '#b1cdbe';");
        $this->addSql("UPDATE company_minisite SET background_color = '#e6e0d1' where background_color = '#ccc6b4';");
        $this->addSql("UPDATE company_minisite SET background_color = '#e1e5eb' where background_color = '#c2c7d0';");
        $this->addSql("UPDATE company_minisite SET background_color = '#f0dfd1' where background_color = '#d6c3b2';");
        $this->addSql("UPDATE company_minisite SET background_color = '#d3e7f5' where background_color = '#b4cadb';");
        $this->addSql("UPDATE company_minisite SET background_color = '#ebe1f5' where background_color = '#cfc2d9';");
        $this->addSql("UPDATE company_minisite SET background_color = '#dedede' where background_color = '#c6c6c6';");


    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
