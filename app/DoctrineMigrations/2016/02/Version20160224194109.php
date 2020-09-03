<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160224194109 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE content_comment ADD photo_id INT DEFAULT NULL, ADD comment_type SMALLINT NOT NULL");
        $this->addSql("ALTER TABLE content_comment ADD CONSTRAINT FK_4B7C8BDF7E9E4C8C FOREIGN KEY (photo_id) REFERENCES instagram_photo (id)");
        $this->addSql("CREATE INDEX IDX_4B7C8BDF7E9E4C8C ON content_comment (photo_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
