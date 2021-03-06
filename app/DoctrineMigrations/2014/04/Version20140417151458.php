<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140417151458 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Classificator_Country ADD callback_phone VARCHAR(30) DEFAULT NULL');
        $this->addSql('UPDATE Classificator_Country SET support_phone = "+7 (495) 268-02-85" WHERE Country_ID IN (165, 209, 83, 19)');
        $this->addSql('UPDATE Classificator_Country SET callback_phone = "8 (800) 555-56-65" WHERE Country_ID IN (165)');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
