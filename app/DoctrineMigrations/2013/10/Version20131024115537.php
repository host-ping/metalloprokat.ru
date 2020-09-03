<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131024115537 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE company_delivery_city (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, city_id INT DEFAULT NULL, kind SMALLINT NOT NULL, INDEX IDX_80E5E8D3979B1AD6 (company_id), INDEX IDX_80E5E8D38BAC62AF (city_id), UNIQUE INDEX UNIQ_category_city (company_id, city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("INSERT INTO company_delivery_city (company_id, city_id, kind) (SELECT Company_ID, City_ID, 0 FROM Message143 GROUP BY Company_ID, City_ID)");

    }

    public function down(Schema $schema)
    {

    }
}
