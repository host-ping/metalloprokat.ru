<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151124152202 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE content_question_tag
            (id INT AUTO_INCREMENT NOT NULL, question_id INT DEFAULT NULL,
            tag_id INT DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_37155A441E27F6BF (question_id),
            INDEX IDX_37155A44BAD26311 (tag_id),
            UNIQUE INDEX UNIQ_question_tag (question_id, tag_id),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ");
        $this->addSql("
            CREATE TABLE content_question
            (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, category_id INT NOT NULL,
            category_secondary_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL, status_type_id INT NOT NULL, description LONGTEXT NOT NULL,
             short_description LONGTEXT NOT NULL, page_title VARCHAR(255) DEFAULT NULL,
             notify TINYINT(1) DEFAULT '0' NOT NULL, email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL,
             INDEX IDX_E440A3A1A76ED395 (user_id), INDEX IDX_E440A3A112469DE2 (category_id),
             INDEX IDX_E440A3A11F4A3B9E (category_secondary_id),
             PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ");

        $this->addSql("ALTER TABLE content_question_tag ADD CONSTRAINT FK_37155A441E27F6BF FOREIGN KEY (question_id) REFERENCES content_question (id) ON DELETE CASCADE;");
        $this->addSql("ALTER TABLE content_question_tag ADD CONSTRAINT FK_37155A44BAD26311 FOREIGN KEY (tag_id) REFERENCES content_tag (id) ON DELETE SET NULL;");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
