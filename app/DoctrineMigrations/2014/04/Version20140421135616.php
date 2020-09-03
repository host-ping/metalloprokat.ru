<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140421135616 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE counter_country CHANGE demands_count demands_count MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE demands_count_recursive demands_count_recursive MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE products_count products_count MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE products_count_recursive products_count_recursive MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE companies_count companies_count MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE companies_count_recursive companies_count_recursive MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL;');
        $this->addSql('ALTER TABLE counter_category_city CHANGE demands_count demands_count MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE demands_count_recursive demands_count_recursive MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE products_count products_count MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE products_count_recursive products_count_recursive MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE companies_count companies_count MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE companies_count_recursive companies_count_recursive MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL;');
        $this->addSql('ALTER TABLE counter_category_country CHANGE demands_count demands_count MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE demands_count_recursive demands_count_recursive MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE products_count products_count MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE products_count_recursive products_count_recursive MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE companies_count companies_count MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE companies_count_recursive companies_count_recursive MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL;');
        $this->addSql('ALTER TABLE counter_city CHANGE demands_count demands_count MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE demands_count_recursive demands_count_recursive MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE products_count products_count MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE products_count_recursive products_count_recursive MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE companies_count companies_count MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL, CHANGE companies_count_recursive companies_count_recursive MEDIUMINT UNSIGNED DEFAULT 0 NOT NULL;');
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
