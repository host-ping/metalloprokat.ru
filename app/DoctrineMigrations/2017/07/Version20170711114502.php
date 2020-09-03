<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\ProductsBundle\Entity\Product;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170711114502 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            'UPDATE Message142 AS product SET product.Checked = :to_checked WHERE product.is_virtual = TRUE AND product.Checked = :from_checked',
            array(
                'to_checked' => Product::STATUS_CHECKED,
                'from_checked' => Product::STATUS_DELETED,
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
