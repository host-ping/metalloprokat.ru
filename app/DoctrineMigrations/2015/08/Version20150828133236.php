<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150828133236 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Classificator_Country SET capital_id = 1123 WHERE Country_ID = 165");
        $this->addSql("UPDATE Classificator_Country SET capital_id = 2368 WHERE Country_ID = 209");
        $this->addSql("UPDATE Classificator_Country SET capital_id = 2377 WHERE Country_ID = 19");
        $this->addSql("UPDATE Classificator_Country SET capital_id = 2418 WHERE Country_ID = 83");
        $this->addSql("UPDATE Classificator_Region SET is_capital = TRUE WHERE Region_ID IN (1123, 2368, 2377, 2418)");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
