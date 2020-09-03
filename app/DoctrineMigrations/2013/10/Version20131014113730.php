<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131014113730 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE company_category_counter (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, category_id INT NOT NULL, products_count INT NOT NULL, products_count_recursive INT NOT NULL, INDEX IDX_280128BD979B1AD6 (company_id), INDEX IDX_280128BDE6ADA943 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("ALTER TABLE company_counter ADD all_products_count INT NOT NULL");

    }

    public function down(Schema $schema)
    {
    }
}
