<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150828130358 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Classificator_Country ADD capital_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE Classificator_Country ADD CONSTRAINT FK_13AAE31FC2D9FF7 FOREIGN KEY (capital_id) REFERENCES Classificator_Region (Region_ID) ON DELETE SET NULL");
        $this->addSql("CREATE INDEX IDX_13AAE31FC2D9FF7 ON Classificator_Country (capital_id)");
        $this->addSql("ALTER TABLE Classificator_Region ADD is_capital TINYINT(1) DEFAULT '0' NOT NULL");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
