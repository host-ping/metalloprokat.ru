<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150817134949 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE product_city
            (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, city_id INT DEFAULT NULL,
            created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A0B320954584665A (product_id),
            INDEX IDX_A0B320958BAC62AF (city_id), UNIQUE INDEX UNIQ_product_city (product_id, city_id),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ");
        $this->addSql("ALTER TABLE product_city ADD CONSTRAINT FK_A0B320954584665A FOREIGN KEY (product_id) REFERENCES product (id)");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
