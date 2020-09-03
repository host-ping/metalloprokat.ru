<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160530143416 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE user_registered_invite_project (
              id         INT AUTO_INCREMENT NOT NULL,
              user_id    INT                NOT NULL,
              created_at DATETIME           NOT NULL,
              INDEX IDX_91814D9EA76ED395 (user_id),
              PRIMARY KEY (id)
            )
              DEFAULT CHARACTER SET utf8
              COLLATE utf8_unicode_ci
              ENGINE = InnoDB;"
        );

        $this->addSql("
            ALTER TABLE user_registered_invite_project
          ADD CONSTRAINT FK_91814D9EA76ED395 FOREIGN KEY (user_id) REFERENCES User (User_ID)
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
