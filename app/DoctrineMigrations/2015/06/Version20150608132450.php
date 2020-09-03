<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150608132450 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE company_old_slug ADD CONSTRAINT FK_A8970FAA979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID) ON DELETE CASCADE");
        $this->addSql("set session old_alter_table=1");
        $this->addSql("ALTER IGNORE TABLE company_old_slug ADD UNIQUE INDEX UNIQ_old_slug (old_slug)");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
