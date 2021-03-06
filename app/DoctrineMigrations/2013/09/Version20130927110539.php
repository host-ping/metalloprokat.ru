<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130927110539 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
       # $this->addSql('DROP TABLE demand_complaint');
        $this->addSql("CREATE TABLE complaint (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, object_id INT NOT NULL, body LONGTEXT DEFAULT NULL, ip VARCHAR(25) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, referer VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, complaint_type INT NOT NULL, complaint_object_type ENUM('demand', 'product') NOT NULL, INDEX IDX_5F2732B5A76ED395 (user_id), INDEX IDX_5F2732B5232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
