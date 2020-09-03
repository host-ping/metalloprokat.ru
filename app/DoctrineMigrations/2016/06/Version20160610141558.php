<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160610141558 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE instagram_stats ADD comment_id INT DEFAULT NULL, ADD commented_at DATETIME DEFAULT NULL, DROP is_commented");
        $this->addSql("CREATE INDEX IDX_62023B34F8697D13 ON instagram_stats (comment_id)");
        $this->addSql("ALTER TABLE instagram_stats ADD CONSTRAINT FK_62023B34F8697D13 FOREIGN KEY (comment_id) REFERENCES instagram_account_tag_comment (id)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
