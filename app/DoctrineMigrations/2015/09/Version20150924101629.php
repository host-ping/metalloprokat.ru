<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\CompaniesBundle\Entity\CompanyCity;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150924101629 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            "UPDATE company_delivery_city AS companyCity SET companyCity.kind = :kind WHERE LENGTH(companyCity.address_new) > :min_length AND companyCity.kind <> :kind",
            array(
                'kind' => CompanyCity::KIND_BRANCH_OFFICE,
                'min_length' => 1
            )
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
