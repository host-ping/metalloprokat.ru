<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\ProductsBundle\Entity\Product;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150519121307 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            UPDATE Message142 AS product
                JOIN Message75 AS company ON company.Message_ID = product.Company_ID
                SET product.Checked = :checked,
                  product.branch_office_id = (
                    SELECT cdc.id FROM company_delivery_city AS cdc
                    WHERE cdc.company_id = company.Message_ID AND cdc.city_id = company.company_city
                  )
            WHERE product.branch_office_id IS NULL
        ", array('checked' => Product::STATUS_DELETED));
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
