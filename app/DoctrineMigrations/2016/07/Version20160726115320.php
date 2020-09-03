<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160726115320 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            ALTER TABLE client_review 
                CHANGE logo photo VARCHAR(255) NOT NULL, 
                CHANGE logo_mime photo_mime VARCHAR(15) DEFAULT NULL, 
                CHANGE logo_original_name photo_original_name VARCHAR(100) DEFAULT NULL
            '
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
