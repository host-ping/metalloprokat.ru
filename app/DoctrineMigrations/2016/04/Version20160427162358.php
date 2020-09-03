<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160427162358 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
                CREATE TABLE instagram_liker (
          id                   INT AUTO_INCREMENT     NOT NULL,
          instagram_account_id INT      DEFAULT NULL,
          instagram_user_id    VARCHAR(255)           NOT NULL,
          user_name            VARCHAR(255)           NOT NULL,
          follow               TINYINT(1) DEFAULT '0' NOT NULL,
          user_full_name       VARCHAR(255)           NOT NULL,
          created_at           DATETIME               NOT NULL,
          updated_at           DATETIME DEFAULT NULL,
          INDEX IDX_B882275A0E6FB4C (instagram_account_id),
          UNIQUE INDEX UNIQ_instagram_user_user_id (instagram_account_id, instagram_user_id),
          PRIMARY KEY (id)
        )
          DEFAULT CHARACTER SET utf8
          COLLATE utf8_unicode_ci
          ENGINE = InnoDB;
        ");
        
        $this->addSql("
                CREATE TABLE instagram_account (
          id         INT AUTO_INCREMENT NOT NULL,
          username   VARCHAR(255)       NOT NULL,
          password   VARCHAR(255)       NOT NULL,
          updated_at DATETIME           NOT NULL,
          created_at DATETIME           NOT NULL,
          PRIMARY KEY (id)
        )
          DEFAULT CHARACTER SET utf8
          COLLATE utf8_unicode_ci
          ENGINE = InnoDB;
        ");
        
        $this->addSql("
            ALTER TABLE instagram_liker ADD CONSTRAINT FK_B882275A0E6FB4C FOREIGN KEY (instagram_account_id) REFERENCES instagram_account (id);
        ");
        
        $this->addSql("
            DROP INDEX UNIQ_code ON instagram_stats;
        ");
        
        $this->addSql("
                ALTER TABLE instagram_stats
          ADD instagram_account_id INT DEFAULT NULL,
          CHANGE user_id instagram_user_id VARCHAR(255) NOT NULL,
          ADD liked_at DATETIME DEFAULT NULL,
          CHANGE is_like is_liked TINYINT(1) DEFAULT '0' NOT NULL;
        ");
        
        $this->addSql("
            ALTER TABLE instagram_stats ADD CONSTRAINT FK_62023B34A0E6FB4C FOREIGN KEY (instagram_account_id) REFERENCES instagram_account (id);
        ");
        
        $this->addSql("
            CREATE INDEX IDX_62023B34A0E6FB4C ON instagram_stats (instagram_account_id);
        ");
        
        $this->addSql("
            CREATE UNIQUE INDEX UNIQ_instagram_account_id_code ON instagram_stats (instagram_account_id, code);
        ");
        
        $this->addSql("
            DROP INDEX UNIQ_215A7E4B2B36786B ON instagram_parse_tag;
        ");
        
        $this->addSql("
            ALTER TABLE instagram_parse_tag ADD instagram_account_id INT DEFAULT NULL, ADD is_automatically_added TINYINT(1) DEFAULT '1' NOT NULL;
        ");
        
        $this->addSql("
            ALTER TABLE instagram_parse_tag ADD CONSTRAINT FK_215A7E4BA0E6FB4C FOREIGN KEY (instagram_account_id) REFERENCES instagram_account (id);
        ");
        
        $this->addSql("
            CREATE INDEX IDX_215A7E4BA0E6FB4C ON instagram_parse_tag (instagram_account_id);
        ");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
