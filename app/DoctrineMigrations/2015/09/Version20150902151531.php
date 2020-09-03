<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150902151531 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE normalize_phones (
              id         INT AUTO_INCREMENT NOT NULL,
              company_id INT DEFAULT NULL,
              user_id    INT DEFAULT NULL,
              phone      INT                NOT NULL,
              INDEX IDX_1F2A8171979B1AD6 (company_id),
              INDEX IDX_1F2A8171A76ED395 (user_id),
              INDEX IDX_phone (phone),
              UNIQUE INDEX UNIQ_phone_company (phone, company_id),
              UNIQUE INDEX UNIQ_phone_user (phone, user_id),
              PRIMARY KEY (id)
            )
              DEFAULT CHARACTER SET utf8
              COLLATE utf8_unicode_ci
              ENGINE = InnoDB;
        ");

        $this->addSql("
              ALTER TABLE normalize_phones ADD CONSTRAINT FK_1F2A8171979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID) ON DELETE CASCADE;
        ");

        $this->addSql("
              ALTER TABLE normalize_phones ADD CONSTRAINT FK_1F2A8171A76ED395 FOREIGN KEY (user_id) REFERENCES User (User_ID) ON DELETE CASCADE;
        ");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
