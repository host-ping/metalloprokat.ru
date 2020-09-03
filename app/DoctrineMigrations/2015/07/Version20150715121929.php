<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150715121929 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE city_code (id INT AUTO_INCREMENT NOT NULL,
            city_id INT NOT NULL, code VARCHAR(50) NOT NULL, INDEX IDX_945B0DF08BAC62AF (city_id),
            UNIQUE INDEX UNIQ_city_code (city_id, code), PRIMARY KEY(id))
            DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql("ALTER TABLE company_delivery_city ADD is_associated_with_city_code TINYINT(1) DEFAULT '0' NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
