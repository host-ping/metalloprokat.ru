<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130923130556 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('DROP TABLE IF EXISTS company_comment');
        $this->addSql("CREATE TABLE company_review (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, user_id INT NOT NULL, answer_id INT DEFAULT NULL, type TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, answered_at DATETIME DEFAULT NULL, INDEX IDX_4370E085979B1AD6 (company_id), INDEX IDX_4370E085A76ED395 (user_id), INDEX IDX_4370E085AA334807 (answer_id), UNIQUE INDEX UNIQ_company_user (company_id, user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE TABLE company_counter (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, reviews_count INT NOT NULL, UNIQUE INDEX UNIQ_E1708EAF979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

    }

    public function down(Schema $schema)
    {

    }
}
