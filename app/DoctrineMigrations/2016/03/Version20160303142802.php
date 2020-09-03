<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160303142802 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE user_history
            (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, author_id INT NOT NULL, created_at DATETIME NOT NULL,
             comment LONGTEXT DEFAULT NULL, action_id INT NOT NULL, INDEX IDX_7FB76E41A76ED395 (user_id), INDEX IDX_7FB76E41F675F31B (author_id),
             PRIMARY KEY(id)
            )
            DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;'
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
