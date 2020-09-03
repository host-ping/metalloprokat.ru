<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150422130056 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE company_phone AS cp JOIN company_delivery_city AS cdc
            ON cp.branch_office_id = cdc.id
            SET cp.company_id = cdc.company_id
            WHERE cp.company_id IS NULL
        ');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
