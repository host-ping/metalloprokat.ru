<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150925185427 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE promocode (
                         id INT AUTO_INCREMENT NOT NULL,
                         company_id INT DEFAULT NULL,
                         code VARCHAR(4) NOT NULL,
                         created_at DATETIME NOT NULL,
                         starts_at DATETIME NOT NULL,
                         ends_at DATETIME NOT NULL,
                         activated_at DATETIME NOT NULL,
                         UNIQUE INDEX UNIQ_7C786E06979B1AD6 (company_id),
                         PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
