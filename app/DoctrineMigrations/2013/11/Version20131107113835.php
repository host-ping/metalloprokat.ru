<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131107113835 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->addSql('
            INSERT INTO company_phone (branch_office_id, phone, company_id, additional_code)
            SELECT dc.id, s.Phone, s.Company_ID, "" FROM Message143 s
              JOIN company_delivery_city dc ON
                s.Company_ID = dc.company_id
                AND s.City_ID = dc.city_id
            WHERE s.Company_ID = 1');
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}