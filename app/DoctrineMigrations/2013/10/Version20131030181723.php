<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131030181723 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE company_review_answer (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL, comment VARCHAR(255) NOT NULL, INDEX IDX_2C892F98A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE company_review ADD CONSTRAINT FK_44DCE5B3AA334807 FOREIGN KEY (answer_id) REFERENCES company_review_answer (id)");
        $this->addSql("ALTER TABLE company_review CHANGE type type TINYINT NOT NULL;");
    }

    public function down(Schema $schema)
    {
    }
}
