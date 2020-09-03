<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130521152947 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Classificator_Region SET Region_Name = trim(Region_Name)");
        $this->addSql("UPDATE Classificator_Region SET Region_Name = replace(Region_Name, char(9), '')");
        $this->addSql("UPDATE Classificator_Region SET Region_Name = replace(Region_Name, char(10), '')");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
