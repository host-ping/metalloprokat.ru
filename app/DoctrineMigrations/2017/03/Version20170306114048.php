<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170306114048 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE custom_category_closure (id INT AUTO_INCREMENT NOT NULL, ancestor INT NOT NULL, descendant INT NOT NULL, depth INT NOT NULL, INDEX IDX_A2BB0EFCB4465BB (ancestor), INDEX IDX_A2BB0EFC9A8FAD16 (descendant), INDEX IDX_D77CE9B1599D7DA4 (depth), UNIQUE INDEX IDX_2C840FE869503970 (ancestor, descendant), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE custom_category_closure ADD CONSTRAINT FK_A2BB0EFCB4465BB FOREIGN KEY (ancestor) REFERENCES custom_company_category (id)');
        $this->addSql('ALTER TABLE custom_category_closure ADD CONSTRAINT FK_A2BB0EFC9A8FAD16 FOREIGN KEY (descendant) REFERENCES custom_company_category (id)');
        $this->addSql('ALTER TABLE custom_company_category ADD branch_ids VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
