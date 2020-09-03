<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130923101528 extends AbstractMigration
{
    public function up(Schema $schema)
    {

        $this->addSql("CREATE TABLE company_comment (company_id INT NOT NULL, user_id INT NOT NULL, answer_id INT DEFAULT NULL, Message_ID INT AUTO_INCREMENT NOT NULL, type TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, answered_at DATETIME NOT NULL, INDEX IDX_B42648BB979B1AD6 (company_id), INDEX IDX_B42648BBA76ED395 (user_id), INDEX IDX_B42648BBAA334807 (answer_id), UNIQUE INDEX UNIQ_company_user (company_id, user_id), PRIMARY KEY(Message_ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
//        $this->addSql("ALTER TABLE company_comment ADD CONSTRAINT FK_B42648BB979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID)");
//        $this->addSql("ALTER TABLE company_comment ADD CONSTRAINT FK_B42648BBA76ED395 FOREIGN KEY (user_id) REFERENCES User (User_ID)");
//        $this->addSql("ALTER TABLE company_comment ADD CONSTRAINT FK_B42648BBAA334807 FOREIGN KEY (answer_id) REFERENCES company_comment (id)");

    }

    public function down(Schema $schema)
    {
    }
}
