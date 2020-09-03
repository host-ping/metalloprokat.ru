<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160418115105 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            ALTER TABLE user_registration_with_parser
          ADD company_id INT NOT NULL
        ");
        
        $this->addSql("
            ALTER TABLE user_registration_with_parser
          ADD CONSTRAINT FK_2A9A1811979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID)
        ");
        
        $this->addSql("
            CREATE UNIQUE INDEX UNIQ_2A9A1811979B1AD6 ON user_registration_with_parser (company_id)
        ");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
