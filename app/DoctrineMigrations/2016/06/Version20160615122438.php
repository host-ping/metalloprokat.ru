<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160615122438 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE content_category SET meta_title = CONCAT(title, ' - обсуждение ремонта, отделки и обустройства квартиры и таунхауса') 
            WHERE meta_title IS NULL AND parent_id = 83
        ");
        $this->addSql("UPDATE content_category SET meta_title = CONCAT(title, ' - обсуждение строительства загородного дома, ремонта, отделки и обустройства дома своими руками') 
            WHERE meta_title IS NULL AND parent_id = 84");
        $this->addSql("UPDATE content_category SET meta_title = CONCAT(title, ' - обсуждения строительства и ремонта строений и сооружений на участке своими руками') 
            WHERE meta_title IS NULL AND parent_id = 85");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
