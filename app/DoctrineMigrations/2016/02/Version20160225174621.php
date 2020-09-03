<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160225174621 extends AbstractMigration
{
    public function up(Schema $schema)
    {

        $this->addSql('ALTER TABLE content_comment DROP FOREIGN KEY FK_4B7C8BDF7E9E4C8C');
        $this->addSql('DROP INDEX IDX_4B7C8BDF7E9E4C8C ON content_comment');

        $this->addSql('ALTER TABLE content_comment DROP FOREIGN KEY FK_4B7C8BDFE881A3AD');
        $this->addSql('ALTER TABLE content_comment DROP photo_id, DROP comment_type, CHANGE content_entry_id content_entry_id INT NOT NULL');
        $this->addSql('ALTER TABLE content_comment ADD CONSTRAINT FK_4B7C8BDFE881A3AD FOREIGN KEY (content_entry_id) REFERENCES content_entry (content_entry_id) ON DELETE CASCADE');

        $this->addSql('CREATE TABLE instagram_comment
            (id INT AUTO_INCREMENT NOT NULL, photo_id INT DEFAULT NULL, parent_id INT DEFAULT NULL, user_id INT DEFAULT NULL, description LONGTEXT NOT NULL,
            email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, notify TINYINT(1) DEFAULT \'0\' NOT NULL, created_at DATETIME NOT NULL,
             updated_at DATETIME NOT NULL, status_type_id INT NOT NULL, INDEX IDX_196BDD627E9E4C8C (photo_id), INDEX IDX_196BDD62727ACA70 (parent_id),
             INDEX IDX_196BDD62A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE instagram_comment ADD CONSTRAINT FK_196BDD627E9E4C8C FOREIGN KEY (photo_id) REFERENCES instagram_photo (id)');
        $this->addSql('ALTER TABLE instagram_comment ADD CONSTRAINT FK_196BDD62727ACA70 FOREIGN KEY (parent_id) REFERENCES instagram_comment (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
