<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\CatalogBundle\Entity\Product;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150921124300 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->getConfiguration()->setSQLLogger(null);
        $connection = $this->connection;

        $connection->executeQuery('INSERT INTO catalog_brand (id, attribute_value_id, title, slug)
            (
            SELECT av.id, av.id, av.value, av.slug FROM attribute_value AS av JOIN attribute AS a ON av.attribute_id = a.id AND a.code = :code
            ) ON DUPLICATE KEY UPDATE title = av.value, slug = av.slug
        ', array('code' => Product::ATTR_CODE_BRAND));

        $connection->executeQuery('INSERT INTO catalog_manufacturer (id, attribute_value_id, title, slug)
            (
            SELECT av.id, av.id, av.value, av.slug FROM attribute_value AS av JOIN attribute AS a ON av.attribute_id = a.id AND a.code = :code
            ) ON DUPLICATE KEY UPDATE title = av.value, slug = av.slug
        ', array('code' => Product::ATTR_CODE_MANUFACTURER));

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
