<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140428184043 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE callback ADD demand_id INT DEFAULT NULL;");
        $this->addSql("ALTER TABLE callback ADD CONSTRAINT FK_79F974265D022E59 FOREIGN KEY (demand_id) REFERENCES demand (id);");
        $this->addSql("CREATE INDEX IDX_79F974265D022E59 ON callback (demand_id);");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}