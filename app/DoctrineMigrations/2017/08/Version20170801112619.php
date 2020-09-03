<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170801112619 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
                CREATE TABLE newsletter_viewer (
          id            INT AUTO_INCREMENT NOT NULL,
          recipient_id  INT                NOT NULL,
          created_at DATETIME NOT NULL,
          INDEX IDX_B906F71CE92F8F78 (recipient_id),
          PRIMARY KEY (id)
        )
          DEFAULT CHARACTER SET utf8
          COLLATE utf8_unicode_ci
          ENGINE = InnoDB
        ");

        $this->addSql("
            ALTER TABLE newsletter_viewer
            ADD CONSTRAINT FK_B906F71CE92F8F78 FOREIGN KEY (recipient_id) REFERENCES newsletter_recipient (id)
        ");

        $this->addSql("ALTER TABLE newsletter_recipient ADD hash_key VARCHAR(40) DEFAULT NULL");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
