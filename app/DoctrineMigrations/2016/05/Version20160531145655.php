<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160531145655 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO instagram_stop_word (title, enabled, created_at) VALUES ('whatsapp', 1, '2016-05-31 14:53:05')");
        $this->addSql("INSERT INTO instagram_stop_word (title, enabled, created_at) VALUES ('viber', 1, '2016-05-31 14:53:05')");
        $this->addSql("INSERT INTO instagram_stop_word (title, enabled, created_at) VALUES ('sms', 1, '2016-05-31 14:53:05')");
        $this->addSql("INSERT INTO instagram_stop_word (title, enabled, created_at) VALUES ('доставка', 1, '2016-05-31 14:53:05')");
        $this->addSql("INSERT INTO instagram_stop_word (title, enabled, created_at) VALUES ('telegram', 1, '2016-05-31 14:53:05')");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
