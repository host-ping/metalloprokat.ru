<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151125194032 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE content_answer
            (id INT AUTO_INCREMENT NOT NULL, topic_id INT DEFAULT NULL, question_id INT DEFAULT NULL, answer_parent INT NOT NULL, description LONGTEXT NOT NULL,
            email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, notify TINYINT(1) DEFAULT '0' NOT NULL, created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL, INDEX IDX_B2EB3A891F55203D (topic_id), INDEX IDX_B2EB3A891E27F6BF (question_id),
            INDEX IDX_B2EB3A89CF775AE8 (answer_parent), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ");

        $this->addSql("ALTER TABLE content_answer ADD CONSTRAINT FK_B2EB3A891F55203D FOREIGN KEY (topic_id) REFERENCES content_topic (id) ON DELETE CASCADE;");
        $this->addSql("ALTER TABLE content_answer ADD CONSTRAINT FK_B2EB3A891E27F6BF FOREIGN KEY (question_id) REFERENCES content_question (id) ON DELETE CASCADE;");
        $this->addSql("ALTER TABLE content_answer ADD CONSTRAINT FK_B2EB3A89CF775AE8 FOREIGN KEY (answer_parent) REFERENCES content_answer (id) ON DELETE CASCADE;");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
