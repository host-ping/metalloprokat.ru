<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\ProductsBundle\Entity\Product;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160205130429 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Message142 SET Price = :price WHERE is_contract_price = 1 AND Price <> :price",
            array('price' => Product::PRICE_CONTRACT)
        );

        $results = $this->connection
            ->executeQuery("SELECT Message_ID AS id, slug FROM Message155 AS po WHERE po.slug <> ''")
            ->fetchAll();

        foreach ($results as $result) {
            $this->connection
                ->executeUpdate("UPDATE Message155 SET slug = :slug WHERE Message_ID = :id",
                array(
                    'id' => $result['id'],
                    'slug' => trim($result['slug'], " \xC2\xA0\n\r\t\v")
                )
            );
        }

        $results = $this->connection
            ->executeQuery("SELECT id, slug, value FROM attribute_value AS av WHERE av.slug <> ''")
            ->fetchAll();

        foreach ($results as $result) {
            $this->connection
                ->executeUpdate("UPDATE attribute_value SET slug = :slug, value = :value WHERE id = :id",
                array(
                    'id' => $result['id'],
                    'slug' => trim($result['slug'], " \xC2\xA0\n\r\t"),
                    'value' => trim($result['value'], " \xC2\xA0\n\r\t"),
                )
            );
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
