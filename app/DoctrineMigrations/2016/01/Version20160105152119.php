<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160105152119 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE normalized_email ADD subscriber_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE normalized_email ADD CONSTRAINT FK_3BCCCEC47808B1AD FOREIGN KEY (subscriber_id) REFERENCES UserSend (ID) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_3BCCCEC47808B1AD ON normalized_email (subscriber_id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_subscriber_email ON normalized_email (subscriber_id, email)");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
