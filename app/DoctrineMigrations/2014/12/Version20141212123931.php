<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141212123931 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Message73 ADD real_category INT DEFAULT NULL");
        $this->addSql("ALTER TABLE Message73 ADD CONSTRAINT FK_6281B256F129715C FOREIGN KEY (real_category) REFERENCES Message73 (Message_ID)");
        $this->addSql("CREATE INDEX IDX_6281B256F129715C ON Message73 (real_category)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
