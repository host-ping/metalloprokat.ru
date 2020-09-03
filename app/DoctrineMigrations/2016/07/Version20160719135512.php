<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160719135512 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
            ALTER TABLE content_category 
            ADD title_genitive VARCHAR(100) DEFAULT NULL,
            ADD title_accusative VARCHAR(100) DEFAULT NULL, 
            ADD title_prepositional VARCHAR(100) DEFAULT NULL, 
            ADD title_ablative VARCHAR(100) DEFAULT NULL
        ');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
