<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151211134512 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql("ALTER TABLE content_answer DROP FOREIGN KEY FK_B2EB3A891E27F6BF");
        $this->addSql("ALTER TABLE content_question_tag DROP FOREIGN KEY FK_37155A441E27F6BF");
        $this->addSql("ALTER TABLE content_answer DROP FOREIGN KEY FK_B2EB3A891F55203D");
        $this->addSql("ALTER TABLE content_topic_tag DROP FOREIGN KEY FK_CD8E221F55203D");
        $this->addSql("CREATE TABLE content_entry (
  content_entry_id int AUTO_INCREMENT NOT NULL,
  entry_type tinyint(1) NOT NULL,
  id int NOT NULL,
  title varchar(255) NOT NULL,
  user_id int DEFAULT NULL,
  category_id int NOT NULL,
  category_secondary_id int DEFAULT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL,
  status_type_id int NOT NULL,
  subject_type_id int NOT NULL,
  description longtext NOT NULL,
  short_description longtext NOT NULL,
  page_title varchar(255) DEFAULT NULL,
  notify tinyint(1) DEFAULT '0' NOT NULL,
  email varchar(255) DEFAULT NULL,
  name varchar(255) DEFAULT NULL,
  INDEX IDX_C0E2C9A2A76ED395 (user_id),
  INDEX IDX_C0E2C9A212469DE2 (category_id),
  INDEX IDX_C0E2C9A21F4A3B9E (category_secondary_id),
  UNIQUE INDEX UNIQ_type_id (entry_type, id),
  PRIMARY KEY (content_entry_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = INNODB");
        $this->addSql("ALTER TABLE content_entry ADD CONSTRAINT FK_C0E2C9A2A76ED395 FOREIGN KEY (user_id) REFERENCES User (User_ID)");
        $this->addSql("ALTER TABLE content_entry ADD CONSTRAINT FK_C0E2C9A212469DE2 FOREIGN KEY (category_id) REFERENCES content_category (id)");
        $this->addSql("ALTER TABLE content_entry ADD CONSTRAINT FK_C0E2C9A21F4A3B9E FOREIGN KEY (category_secondary_id) REFERENCES content_category (id)");

        $this->addSql("DROP TABLE content_question");
        $this->addSql("DROP TABLE content_topic");
    }

    public function down(Schema $schema)
    {
    }
}
