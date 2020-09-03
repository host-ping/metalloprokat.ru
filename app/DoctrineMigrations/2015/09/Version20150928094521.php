<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150928094521 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE promocode CHANGE activated_at activated_at DATETIME DEFAULT NULL");
        $this->addSql("ALTER TABLE promocode ADD CONSTRAINT FK_7C786E06979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
