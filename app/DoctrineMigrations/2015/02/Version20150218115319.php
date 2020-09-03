<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150218115319 extends AbstractMigration
{
    public function up(Schema $schema)
    {
       $this->addSql("ALTER TABLE url_rewrite ADD CONSTRAINT FK_B8985B8B12469DE2 FOREIGN KEY (category_id) REFERENCES Message73 (Message_ID) ON DELETE CASCADE");
       $this->addSql("ALTER TABLE url_rewrite ADD CONSTRAINT FK_B8985B8B979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID)");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
