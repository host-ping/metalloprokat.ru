<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160223134436 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE instagram_photo
            (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(1000) NOT NULL,
            description VARCHAR(255) DEFAULT NULL, tags LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\',
            mime_type VARCHAR(15) DEFAULT NULL, file_size INT DEFAULT NULL, INDEX IDX_21F2D886A76ED395 (user_id),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ');
        $this->addSql('CREATE TABLE instagram_user_tag
            (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_FAAE480A76ED395 (user_id),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;'
        );
        $this->addSql('CREATE TABLE instagram_user (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL,
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ');
        $this->addSql('ALTER TABLE instagram_photo ADD CONSTRAINT FK_21F2D886A76ED395 FOREIGN KEY (user_id) REFERENCES instagram_user (id);');
        $this->addSql('ALTER TABLE instagram_user_tag ADD CONSTRAINT FK_FAAE480A76ED395 FOREIGN KEY (user_id) REFERENCES instagram_user (id);');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
