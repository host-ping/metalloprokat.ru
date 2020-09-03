<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150814185601 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE product
            (id INT AUTO_INCREMENT NOT NULL, manufacturer_id INT NOT NULL, brand_id INT NOT NULL, attributes_values INT NOT NULL,
            title VARCHAR(255) NOT NULL, size VARCHAR(255) NOT NULL, description VARCHAR(5000) DEFAULT '' NOT NULL, created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL, Category_ID INT DEFAULT NULL, INDEX IDX_D34A04AD3A30165F (Category_ID),
            INDEX IDX_D34A04ADA23B42D (manufacturer_id), INDEX IDX_D34A04AD44F5D008 (brand_id),
            INDEX IDX_D34A04ADE6D185B8 (attributes_values), PRIMARY KEY(id))
            DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");

        $this->addSql("ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA23B42D FOREIGN KEY (manufacturer_id) REFERENCES attribute_value (id) ON DELETE CASCADE;");
        $this->addSql("ALTER TABLE product ADD CONSTRAINT FK_D34A04AD44F5D008 FOREIGN KEY (brand_id) REFERENCES attribute_value (id) ON DELETE CASCADE;");
        $this->addSql("ALTER TABLE product ADD CONSTRAINT FK_D34A04ADE6D185B8 FOREIGN KEY (attributes_values) REFERENCES attribute_value (id) ON DELETE CASCADE;");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
