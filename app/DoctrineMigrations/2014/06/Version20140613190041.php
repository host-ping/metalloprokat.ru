<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140613190041 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        //Украина
        $this->addSql('UPDATE Classificator_Region SET population = 2845683 WHERE Region_ID = 2368');
        $this->addSql('UPDATE Classificator_Region SET population = 1452228 WHERE Region_ID = 2395');
        $this->addSql('UPDATE Classificator_Region SET population = 1014852 WHERE Region_ID = 2379');
        $this->addSql('UPDATE Classificator_Region SET population = 1000111 WHERE Region_ID = 2363');
        //Казахстан
        $this->addSql('UPDATE Classificator_Region SET population = 1600000 WHERE Region_ID = 2351');
        //Беларусь
        $this->addSql('UPDATE Classificator_Region SET population = 1917344 WHERE Region_ID = 2377');
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
