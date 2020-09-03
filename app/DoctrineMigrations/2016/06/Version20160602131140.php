<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160602131140 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE company_counter CHANGE all_products_count products_count MEDIUMINT UNSIGNED NOT NULL DEFAULT '0'");
        $this->addSql("ALTER TABLE company_counter ADD all_products_count MEDIUMINT UNSIGNED NOT NULL DEFAULT '0'");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
