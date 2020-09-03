<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140226130217 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('DROP TABLE announcement_rotation_item');
        $this->addSql('CREATE TABLE client_ip (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, company_id INT DEFAULT NULL, ip VARCHAR(15) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_331BD5CA5E3B32D (ip), INDEX IDX_331BD5CA76ED395 (user_id), INDEX IDX_331BD5C979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema)
    {

    }
}
