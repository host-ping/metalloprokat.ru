<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160817122231 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE landing_page (
                id INT AUTO_INCREMENT NOT NULL, 
                category_id INT NOT NULL, 
                city_id INT DEFAULT NULL, 
                region_id INT DEFAULT NULL, 
                country_id INT DEFAULT NULL, 
                title VARCHAR(255) NOT NULL, 
                slug VARCHAR(255) NOT NULL, 
                enabled TINYINT(1) DEFAULT '1' NOT NULL, 
                results_count INT DEFAULT 0 NOT NULL, 
                created_at DATETIME NOT NULL, 
                results_count_updated_at DATETIME NOT NULL, 
                UNIQUE INDEX UNIQ_87A7C899989D9B62 (slug), 
                INDEX IDX_87A7C89912469DE2 (category_id), 
                INDEX IDX_87A7C8998BAC62AF (city_id), 
                INDEX IDX_87A7C89998260155 (region_id), 
                INDEX IDX_87A7C899F92F3E70 (country_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ");
        $this->addSql('
            CREATE TABLE landing_page_attribute_value (
                id INT AUTO_INCREMENT NOT NULL, 
                landing_page_id INT NOT NULL, 
                attribute_value_id INT NOT NULL, 
                INDEX IDX_5903D82EDF122DC5 (landing_page_id), 
                INDEX IDX_5903D82E65A22152 (attribute_value_id), 
                UNIQUE INDEX UNIQ_landing_page_attribute_value (landing_page_id, attribute_value_id), 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
        ');
        $this->addSql('ALTER TABLE landing_page ADD CONSTRAINT FK_87A7C89912469DE2 FOREIGN KEY (category_id) REFERENCES Message73 (Message_ID) ON DELETE CASCADE;');
        $this->addSql('ALTER TABLE landing_page_attribute_value ADD CONSTRAINT FK_5903D82EDF122DC5 FOREIGN KEY (landing_page_id) REFERENCES landing_page (id);');
        $this->addSql('ALTER TABLE landing_page_attribute_value ADD CONSTRAINT FK_5903D82E65A22152 FOREIGN KEY (attribute_value_id) REFERENCES attribute_value (id);');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
