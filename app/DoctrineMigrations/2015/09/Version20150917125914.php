<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150917125914 extends AbstractMigration
{
    public function up(Schema $schema)
    {


        $this->addSql("CREATE TABLE brand_city (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, city_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A3C088B44F5D008 (brand_id), INDEX IDX_A3C088B8BAC62AF (city_id), UNIQUE INDEX UNIQ_brand_city (brand_id, city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");
        $this->addSql("CREATE TABLE manufacturer_city (id INT AUTO_INCREMENT NOT NULL, manufacturer_id INT DEFAULT NULL, city_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A81033FBA23B42D (manufacturer_id), INDEX IDX_A81033FB8BAC62AF (city_id), UNIQUE INDEX UNIQ_manufacturer_city (manufacturer_id, city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");

        $this->addSql("ALTER TABLE brand_city ADD CONSTRAINT FK_A3C088B44F5D008 FOREIGN KEY (brand_id) REFERENCES catalog_brand (id)");
        $this->addSql("ALTER TABLE manufacturer_city ADD CONSTRAINT FK_A81033FBA23B42D FOREIGN KEY (manufacturer_id) REFERENCES catalog_manufacturer (id)");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
