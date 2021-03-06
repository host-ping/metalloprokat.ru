<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131130112216 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");

        $this->addSql("CREATE TABLE counter_category_city (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, city_id INT NOT NULL, demands_count INT DEFAULT 0 NOT NULL, demands_count_recursive INT DEFAULT 0 NOT NULL, products_count INT DEFAULT 0 NOT NULL, products_count_recursive INT DEFAULT 0 NOT NULL, companies_count INT DEFAULT 0 NOT NULL, companies_count_recursive INT DEFAULT 0 NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_905FCAA212469DE2 (category_id), INDEX IDX_905FCAA28BAC62AF (city_id), UNIQUE INDEX UNIQ_category_city (category_id, city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE counter_category_country (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, country_id INT NOT NULL, demands_count INT DEFAULT 0 NOT NULL, demands_count_recursive INT DEFAULT 0 NOT NULL, products_count INT DEFAULT 0 NOT NULL, products_count_recursive INT DEFAULT 0 NOT NULL, companies_count INT DEFAULT 0 NOT NULL, companies_count_recursive INT DEFAULT 0 NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_AEA394C612469DE2 (category_id), INDEX IDX_AEA394C6F92F3E70 (country_id), UNIQUE INDEX UNIQ_category_country (category_id, country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE counter_city (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, demands_count INT DEFAULT 0 NOT NULL, demands_count_recursive INT DEFAULT 0 NOT NULL, products_count INT DEFAULT 0 NOT NULL, products_count_recursive INT DEFAULT 0 NOT NULL, companies_count INT DEFAULT 0 NOT NULL, companies_count_recursive INT DEFAULT 0 NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_62D6DB538BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE counter_country (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, demands_count INT DEFAULT 0 NOT NULL, demands_count_recursive INT DEFAULT 0 NOT NULL, products_count INT DEFAULT 0 NOT NULL, products_count_recursive INT DEFAULT 0 NOT NULL, companies_count INT DEFAULT 0 NOT NULL, companies_count_recursive INT DEFAULT 0 NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_DD189547F92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
    }

    public function down(Schema $schema)
    {
    }
}
