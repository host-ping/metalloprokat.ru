<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150818124005 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE catalog_product_attribute_value
            (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, attribute_value_id INT NOT NULL,
            INDEX IDX_26B7F47F4584665A (product_id), INDEX IDX_26B7F47F65A22152 (attribute_value_id),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");

        $this->addSql("ALTER TABLE catalog_product_attribute_value ADD CONSTRAINT FK_26B7F47F4584665A FOREIGN KEY (product_id) REFERENCES product (id)");
        $this->addSql("ALTER TABLE catalog_product_attribute_value ADD CONSTRAINT FK_26B7F47F65A22152 FOREIGN KEY (attribute_value_id) REFERENCES attribute_value (id)");

        $this->addSql(" ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADE6D185B8;");
        $this->addSql("DROP INDEX IDX_D34A04ADE6D185B8 ON product;");
        $this->addSql("ALTER TABLE product DROP attributes_values;");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
