<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170320122315 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE custom_category_closure DROP FOREIGN KEY FK_A2BB0EFC9A8FAD16');
        $this->addSql('ALTER TABLE custom_category_closure DROP FOREIGN KEY FK_A2BB0EFCB4465BB');

        $this->addSql('ALTER TABLE custom_category_closure ADD CONSTRAINT FK_A2BB0EFC9A8FAD16 FOREIGN KEY (descendant) REFERENCES custom_company_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE custom_category_closure ADD CONSTRAINT FK_A2BB0EFCB4465BB FOREIGN KEY (ancestor) REFERENCES custom_company_category (id) ON DELETE CASCADE');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
