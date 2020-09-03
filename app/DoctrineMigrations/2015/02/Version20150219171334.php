<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150219171334 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Message142 p SET p.Category_ID = NULL WHERE p.Category_ID IS NOT NULL AND NOT EXISTS (SELECT c.Message_ID FROM Message73 c WHERE c.Message_ID = p.Category_ID)");
        $this->addSql("UPDATE Message142 p SET p.P_Category_ID = NULL WHERE p.P_Category_ID IS NOT NULL AND NOT EXISTS (SELECT c.Message_ID FROM Message73 c WHERE c.Message_ID = p.P_Category_ID)");
        $this->addSql("ALTER TABLE Message142 ADD CONSTRAINT FK_D373210B3A30165F FOREIGN KEY (Category_ID) REFERENCES Message73 (Message_ID) ON DELETE SET NULL");
        $this->addSql("ALTER TABLE Message142 ADD CONSTRAINT FK_D373210B19A6133D FOREIGN KEY (P_Category_ID) REFERENCES Message73 (Message_ID) ON DELETE SET NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
