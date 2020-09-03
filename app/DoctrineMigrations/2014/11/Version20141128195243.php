<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141128195243 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE company_history (id INT AUTO_INCREMENT NOT NULL, company_id INT DEFAULT NULL, related_company_id INT DEFAULT NULL, user_id INT DEFAULT NULL, author_id INT DEFAULT NULL, created_at DATETIME NOT NULL, comment LONGTEXT DEFAULT NULL, action_id INT NOT NULL, INDEX IDX_7E86A9C979B1AD6 (company_id), INDEX IDX_7E86A9C1D561266 (related_company_id), INDEX IDX_7E86A9CA76ED395 (user_id), INDEX IDX_7E86A9CF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
