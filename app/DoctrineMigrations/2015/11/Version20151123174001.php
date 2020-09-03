<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151123174001 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE content_topic_tag
            (id INT AUTO_INCREMENT NOT NULL, topic_id INT DEFAULT NULL, tag_id INT DEFAULT NULL,
            created_at DATETIME NOT NULL, INDEX IDX_CD8E221F55203D (topic_id),
            INDEX IDX_CD8E22BAD26311 (tag_id), UNIQUE INDEX UNIQ_topic_tag (topic_id, tag_id),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ");
        $this->addSql("
            CREATE TABLE content_topic
            (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, category_id INT NOT NULL,
            category_secondary_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL, status_type_id INT NOT NULL, description LONGTEXT NOT NULL,
            short_description LONGTEXT NOT NULL, page_title VARCHAR(255) DEFAULT NULL,
            notify TINYINT(1) DEFAULT '0' NOT NULL, INDEX IDX_76838AC9A76ED395 (user_id),
            INDEX IDX_76838AC912469DE2 (category_id), INDEX IDX_76838AC91F4A3B9E (category_secondary_id),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ");
        $this->addSql("ALTER TABLE content_topic_tag ADD CONSTRAINT FK_CD8E221F55203D FOREIGN KEY (topic_id) REFERENCES content_topic (id) ON DELETE CASCADE;");
        $this->addSql("ALTER TABLE content_topic_tag ADD CONSTRAINT FK_CD8E22BAD26311 FOREIGN KEY (tag_id) REFERENCES content_tag (id) ON DELETE SET NULL;");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
