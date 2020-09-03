<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151211142250 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE content_entry_tag (id INT AUTO_INCREMENT NOT NULL, content_entry_id INT NOT NULL, tag_id INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_C0D281E6E881A3AD (content_entry_id), INDEX IDX_C0D281E6BAD26311 (tag_id), UNIQUE INDEX UNIQ_content_entry_tag (content_entry_id, tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("DROP TABLE content_question_tag");
        $this->addSql("DROP TABLE content_topic_tag");
        $this->addSql("ALTER TABLE content_tag CHANGE COLUMN status_type_id status_type_id TINYINT(1) NOT NULL;");
        $this->addSql("ALTER TABLE content_entry CHANGE COLUMN status_type_id status_type_id TINYINT(1) NOT NULL;");
        $this->addSql("ALTER TABLE content_entry CHANGE COLUMN subject_type_id subject_type_id TINYINT(1) NOT NULL;");

        $this->addSql("ALTER TABLE content_entry_tag ADD CONSTRAINT FK_C0D281E6E881A3AD FOREIGN KEY (content_entry_id) REFERENCES content_entry (content_entry_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE content_entry_tag ADD CONSTRAINT FK_C0D281E6BAD26311 FOREIGN KEY (tag_id) REFERENCES content_tag (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
    }
}
