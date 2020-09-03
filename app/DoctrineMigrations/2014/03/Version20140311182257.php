<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140311182257 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE service_packages (
              id INT AUTO_INCREMENT NOT NULL,
              company_id INT NOT NULL,
              city_id INT DEFAULT NULL,
              package_type_id INT NOT NULL,
              package_period DATE NOT NULL,
              created_at DATETIME NOT NULL,
              start_at DATETIME NOT NULL,
              comment LONGTEXT NOT NULL,
              first_name VARCHAR(255) NOT NULL,
              last_name VARCHAR(255) NOT NULL,
              email VARCHAR(40) NOT NULL,
              phone VARCHAR(255) NOT NULL,
              INDEX IDX_62C39B9A979B1AD6 (company_id),
              INDEX IDX_62C39B9A8BAC62AF (city_id),
              PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
