<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160602162418 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE instagram_stats ADD user_posted INT DEFAULT NULL, ADD instagram_account_posted INT DEFAULT NULL");
        $this->addSql("ALTER TABLE instagram_stats ADD CONSTRAINT FK_62023B34BCF7A29E FOREIGN KEY (user_posted) REFERENCES User (User_ID)");
        $this->addSql("ALTER TABLE instagram_stats ADD CONSTRAINT FK_62023B34BCCBC435 FOREIGN KEY (instagram_account_posted) REFERENCES instagram_account (id)");
        $this->addSql("CREATE INDEX IDX_62023B34BCF7A29E ON instagram_stats (user_posted)");
        $this->addSql("CREATE INDEX IDX_62023B34BCCBC435 ON instagram_stats (instagram_account_posted)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
