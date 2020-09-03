<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\CompaniesBundle\Entity\CompanyCity;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161108162350 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE company_delivery_city AS cdc SET cdc.kind = :kind WHERE cdc.is_main_office = 1',
            array(
            'kind' => CompanyCity::KIND_BRANCH_OFFICE
            )
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
