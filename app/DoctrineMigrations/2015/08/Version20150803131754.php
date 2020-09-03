<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150803131754 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE ban_ip (int_ip INT UNSIGNED NOT NULL, ip VARCHAR(15) NOT NULL, created_at DATETIME NOT NULL, hostname VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(int_ip)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
