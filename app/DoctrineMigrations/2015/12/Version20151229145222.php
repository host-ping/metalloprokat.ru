<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151229145222 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE content_image_album (
            id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL,
            priority SMALLINT DEFAULT '0' NOT NULL, checked TINYINT(1) DEFAULT '1' NOT NULL, created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL, INDEX IDX_7DF7406CA76ED395 (user_id),
            INDEX IDX_7DF7406C12469DE2 (category_id), PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ");
        $this->addSql("CREATE TABLE content_image (
            id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, album_id INT NOT NULL, priority SMALLINT DEFAULT '0' NOT NULL,
            checked TINYINT(1) DEFAULT '1' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, mime_type VARCHAR(15) NOT NULL,
            file_size INT NOT NULL, file_name VARCHAR(255) NOT NULL, file_original_name VARCHAR(100) DEFAULT NULL,
            INDEX IDX_2EFE508DA76ED395 (user_id), INDEX IDX_2EFE508D1137ABCF (album_id), PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ");
        $this->addSql("ALTER TABLE content_image_album ADD CONSTRAINT FK_7DF7406C12469DE2 FOREIGN KEY (category_id) REFERENCES content_category (id)");
        $this->addSql("ALTER TABLE content_image ADD CONSTRAINT FK_2EFE508D1137ABCF FOREIGN KEY (album_id) REFERENCES content_image_album (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
