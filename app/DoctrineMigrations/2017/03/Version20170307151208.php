<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170307151208 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('DROP INDEX UNIQ_33BFCFFF989D9B62 ON custom_company_category');
        $this->addSql('ALTER TABLE custom_company_category ADD company_id INT NOT NULL');
        $this->addSql('ALTER TABLE custom_company_category ADD CONSTRAINT FK_33BFCFFF979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_33BFCFFF979B1AD6 ON custom_company_category (company_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_company_slug ON custom_company_category (company_id, slug)');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
