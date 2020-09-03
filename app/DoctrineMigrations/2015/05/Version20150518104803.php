<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150518104803 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE UserSend ADD bounced_at DATETIME DEFAULT NULL");
        $this->addSql("UPDATE UserSend AS us SET us.bounced_at = us.reviewed_bounce_at WHERE us.is_bounced = true");
        $this->addSql("ALTER TABLE UserSend DROP is_bounced");
        $this->addSql("ALTER TABLE UserSend DROP reviewed_bounce_at");
        $this->addSql("CREATE INDEX IDX_bounced_at ON UserSend (bounced_at)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
