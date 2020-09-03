<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\CompaniesBundle\Entity\CompanyCity;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150826145843 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            "
                    UPDATE Message75 AS company
                     JOIN company_delivery_city AS companyCity
                       ON company.Message_ID = companyCity.company_id
                          AND companyCity.is_main_office = TRUE
                          AND companyCity.kind <> :kind
                    SET companyCity.kind = :kind;
                    ",
            array(
                'kind' => CompanyCity::KIND_BRANCH_OFFICE
            )
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
