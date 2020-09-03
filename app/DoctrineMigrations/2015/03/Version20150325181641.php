<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150325181641 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            "CREATE TABLE grabber_log (id INT AUTO_INCREMENT NOT NULL, site_id INT DEFAULT NULL, level SMALLINT NOT NULL, message VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, context LONGTEXT NOT NULL COMMENT '(DC2Type:json_array)', INDEX IDX_1D108B7AF6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;"
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
