<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151202195633 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            '
            CREATE TABLE content_category_closure
            (id INT AUTO_INCREMENT NOT NULL, ancestor INT NOT NULL, descendant INT NOT NULL, depth INT NOT NULL,
            INDEX IDX_37E3BC5DB4465BB (ancestor), INDEX IDX_37E3BC5D9A8FAD16 (descendant), INDEX IDX_BE8374084E0F95EE (depth),
            UNIQUE INDEX IDX_E1864776D481E971 (ancestor, descendant),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        '
        );
        $this->addSql(
            'ALTER TABLE content_category_closure ADD CONSTRAINT FK_37E3BC5DB4465BB FOREIGN KEY (ancestor) REFERENCES content_category (id);'
        );
        $this->addSql(
            'ALTER TABLE content_category_closure ADD CONSTRAINT FK_37E3BC5D9A8FAD16 FOREIGN KEY (descendant) REFERENCES content_category (id);'
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
