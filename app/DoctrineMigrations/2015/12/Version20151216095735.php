<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151216095735 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE normalized_email ADD company_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE normalized_email ADD CONSTRAINT FK_3BCCCEC4979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_3BCCCEC4979B1AD6 ON normalized_email (company_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
