<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170214134603 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('DROP TABLE IF EXISTS custom_company_category');
        $this->addSql("CREATE TABLE custom_company_category (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, slug_combined VARCHAR(255) DEFAULT '' NOT NULL, display_position SMALLINT DEFAULT 100 NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_33BFCFFF989D9B62 (slug), INDEX IDX_33BFCFFF727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql('ALTER TABLE custom_company_category ADD CONSTRAINT FK_33BFCFFF727ACA70 FOREIGN KEY (parent_id) REFERENCES custom_company_category (id) ON DELETE CASCADE');
        $this->addSql('
            ALTER TABLE Message142 ADD custom_category_id INT DEFAULT NULL,
            ADD CONSTRAINT FK_D373210B247A2828 FOREIGN KEY (custom_category_id) REFERENCES custom_company_category (id) ON DELETE SET NULL,
            ADD INDEX IDX_D373210B247A2828 (custom_category_id)
        ');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
