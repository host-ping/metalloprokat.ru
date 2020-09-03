<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151126175245 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('INSERT IGNORE INTO catalog_product_attribute_value (product_id, attribute_value_id)
            SELECT id, manufacturer_id FROM catalog_product
        ');

        $this->addSql('INSERT IGNORE INTO catalog_product_attribute_value (product_id, attribute_value_id)
            SELECT id, brand_id FROM catalog_product
        ');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
