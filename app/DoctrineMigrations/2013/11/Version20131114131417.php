<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131114131417 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('
            insert ignore INTO company_attribute (company_id, has_production, has_cutting, has_bending, has_delivery)
            (select Message_ID, 0, 0, 0, 0 FROM Message75)');

        $this->addSql('
            UPDATE company_attribute
            set has_cutting = 1
            WHERE company_id IN (
                select DISTINCT Company_ID
                from Message142
                WHERE Category_ID = 206 OR P_Category_ID = 206)');
    }

    public function down(Schema $schema)
    {

    }
}
