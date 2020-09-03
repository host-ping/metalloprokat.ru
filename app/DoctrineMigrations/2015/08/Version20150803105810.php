<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150803105810 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE ban_request (id INT AUTO_INCREMENT NOT NULL, int_ip INT UNSIGNED NOT NULL, int_created_at INT UNSIGNED NOT NULL, ip VARCHAR(15) NOT NULL, created_at DATETIME NOT NULL, uri VARCHAR(255) NOT NULL, INDEX IDX_created_ip (int_ip, int_created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
