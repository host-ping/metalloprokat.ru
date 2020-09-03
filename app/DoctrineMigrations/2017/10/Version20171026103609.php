<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171026103609 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE seo_template (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) NOT NULL, text_block VARCHAR(1000) NOT NULL, meta_title VARCHAR(255) DEFAULT NULL, h1_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(1000) DEFAULT NULL, meta_keywords VARCHAR(255) DEFAULT NULL, INDEX IDX_76C7BEA112469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seo_template_attribute (id INT AUTO_INCREMENT NOT NULL, seo_template_id INT NOT NULL, attribute_id INT NOT NULL, attribute_value_id INT DEFAULT NULL, INDEX IDX_8E019F39C1BD2634 (seo_template_id), INDEX IDX_8E019F39B6E62EFA (attribute_id), INDEX IDX_8E019F3965A22152 (attribute_value_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('ALTER TABLE seo_template ADD CONSTRAINT FK_76C7BEA112469DE2 FOREIGN KEY (category_id) REFERENCES Message73 (Message_ID) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE seo_template_attribute ADD CONSTRAINT FK_8E019F39C1BD2634 FOREIGN KEY (seo_template_id) REFERENCES seo_template (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE seo_template_attribute ADD CONSTRAINT FK_8E019F39B6E62EFA FOREIGN KEY (attribute_id) REFERENCES attribute (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE seo_template_attribute ADD CONSTRAINT FK_8E019F3965A22152 FOREIGN KEY (attribute_value_id) REFERENCES attribute_value (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
