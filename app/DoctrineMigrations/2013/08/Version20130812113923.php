<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130812113923 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE Classificator_Region SET population = 11980000 WHERE Region_ID = 1123');
        $this->addSql('UPDATE Classificator_Region SET population = 5028000 WHERE Region_ID = 1566');
        $this->addSql('UPDATE Classificator_Region SET population = 1524000 WHERE Region_ID = 1255');
        $this->addSql('UPDATE Classificator_Region SET population = 1396000 WHERE Region_ID = 504');
        $this->addSql('UPDATE Classificator_Region SET population = 1260000 WHERE Region_ID = 1200');
        $this->addSql('UPDATE Classificator_Region SET population = 1176000 WHERE Region_ID = 666');
        $this->addSql('UPDATE Classificator_Region SET population = 1172000 WHERE Region_ID = 1560');
        $this->addSql('UPDATE Classificator_Region SET population = 1161000 WHERE Region_ID = 1321');
        $this->addSql('UPDATE Classificator_Region SET population = 1156000 WHERE Region_ID = 2000');
        $this->addSql('UPDATE Classificator_Region SET population = 1104000 WHERE Region_ID = 1539');
        $this->addSql('UPDATE Classificator_Region SET population = 1077000 WHERE Region_ID = 1925');
        $this->addSql('UPDATE Classificator_Region SET population = 1019000 WHERE Region_ID = 334');
        $this->addSql('UPDATE Classificator_Region SET population = 1016000 WHERE Region_ID = 895');
        $this->addSql('UPDATE Classificator_Region SET population = 1014000 WHERE Region_ID = 1395');
        $this->addSql('UPDATE Classificator_Region SET population = 1004000 WHERE Region_ID = 353');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}