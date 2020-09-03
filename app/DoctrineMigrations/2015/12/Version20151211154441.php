<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151211154441 extends AbstractMigration
{
    public function up(Schema $schema)
    {

        $this->addSql("ALTER TABLE content_answer DROP FOREIGN KEY FK_B2EB3A89727ACA70");
        $this->addSql("DROP TABLE content_answer");

        $this->addSql("CREATE TABLE content_comment (id INT AUTO_INCREMENT NOT NULL, content_entry_id INT NOT NULL, user_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, description TEXT NOT NULL, email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, notify TINYINT(1) DEFAULT '0' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_4B7C8BDFE881A3AD (content_entry_id), INDEX IDX_4B7C8BDFA76ED395 (user_id), INDEX IDX_4B7C8BDF727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE content_comment ADD CONSTRAINT FK_4B7C8BDFE881A3AD FOREIGN KEY (content_entry_id) REFERENCES content_entry (content_entry_id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE content_comment ADD CONSTRAINT FK_4B7C8BDFA76ED395 FOREIGN KEY (user_id) REFERENCES User (User_ID)");
        $this->addSql("ALTER TABLE content_comment ADD CONSTRAINT FK_4B7C8BDF727ACA70 FOREIGN KEY (parent_id) REFERENCES content_comment (id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
    }
}
