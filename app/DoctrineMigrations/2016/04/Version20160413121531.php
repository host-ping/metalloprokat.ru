<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160413121531 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE support_topic CHANGE title title VARCHAR(255) DEFAULT '' NOT NULL, CHANGE description description VARCHAR(7150) NOT NULL;
        ");
        
        $this->addSql("
           ALTER TABLE support_topic ADD CONSTRAINT FK_6FFAC8C3F675F31B FOREIGN KEY (author_id) REFERENCES User (User_ID);
        ");
        
        $this->addSql("
            ALTER TABLE support_topic ADD CONSTRAINT FK_6FFAC8C357EB21F9 FOREIGN KEY (resolved_by) REFERENCES User (User_ID);
        ");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
