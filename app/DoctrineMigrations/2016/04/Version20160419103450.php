<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160419103450 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE parser_category_associate 
            (parser_category_id INT AUTO_INCREMENT NOT NULL, 
            category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, 
            parser_category_url VARCHAR(255) DEFAULT NULL,
            created_at DATETIME DEFAULT NULL, INDEX IDX_8007482812469DE2 (category_id), 
            PRIMARY KEY(parser_category_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ");
        
        $this->addSql("
                ALTER TABLE parser_category_associate
          ADD CONSTRAINT FK_8007482812469DE2 FOREIGN KEY (category_id) REFERENCES Message73 (Message_ID)
          ON DELETE SET NULL;
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
