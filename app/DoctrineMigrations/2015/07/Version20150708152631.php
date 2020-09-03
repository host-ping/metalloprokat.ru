<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150708152631 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE  `UserSend` ADD  `subscribed_on_price_invite_emails` TINYINT NOT NULL DEFAULT  '1'");
        $this->addSql("ALTER TABLE  `UserSend` ADD  `price_invite_email_sent_at` DATETIME NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
