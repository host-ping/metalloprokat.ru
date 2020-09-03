<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151202193633 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE content_category
            (id INT AUTO_INCREMENT NOT NULL, parent_id INT NOT NULL, title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) DEFAULT '' NOT NULL, slug_combined VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL, branch_ids VARCHAR(255) NOT NULL, meta_title VARCHAR(255) DEFAULT NULL,
            meta_description VARCHAR(255) DEFAULT NULL, meta_keywords VARCHAR(255) DEFAULT NULL, is_enabled TINYINT(1) DEFAULT '1' NOT NULL,
            priority INT DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_54FBF32E1687DFA3 (slug_combined),
            INDEX IDX_54FBF32E727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ");
        $this->addSql('ALTER TABLE content_category ADD CONSTRAINT FK_54FBF32E727ACA70 FOREIGN KEY (parent_id) REFERENCES content_category (id) ON DELETE CASCADE');


    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
