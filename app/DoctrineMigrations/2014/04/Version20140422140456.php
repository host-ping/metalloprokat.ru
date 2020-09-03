<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140422140456 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE service_packages CHANGE company_title company_title VARCHAR(100) DEFAULT NULL");
        $this->addSql("ALTER TABLE service_packages CHANGE email email VARCHAR(40) DEFAULT NULL");
        $this->addSql("ALTER TABLE service_packages CHANGE city_id city_id INT DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
