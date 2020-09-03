<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\CompaniesBundle\Entity\CompanyCity;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150924120838 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            'UPDATE company_delivery_city AS companyCity SET companyCity.kind = IF(LENGTH(companyCity.address_new) > :min_length, :kind_office, :kind_delivery)
              WHERE companyCity.is_main_office = FALSE',
            array(
                'min_length' => 1,
                'kind_office' => CompanyCity::KIND_BRANCH_OFFICE,
                'kind_delivery' => CompanyCity::KIND_DELIVERY
            )
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
