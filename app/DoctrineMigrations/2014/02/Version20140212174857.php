<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140212174857 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE announcement ADD COLUMN daily_views_count INT NOT NULL');
        $this->addSql('UPDATE announcement SET daily_views_count = 0');
        $this->addSql('CREATE TABLE announcement_stats_element (id INT AUTO_INCREMENT NOT NULL, city_id INT NOT NULL, user_id INT DEFAULT NULL, announcement_id INT DEFAULT NULL, action SMALLINT NOT NULL, source_type_id SMALLINT NOT NULL, ip VARCHAR(25) NOT NULL, user_agent VARCHAR(255) NOT NULL, referer VARCHAR(255) DEFAULT NULL, session_id VARCHAR(64) DEFAULT NULL, created_at DATETIME NOT NULL, date_created_at DATE NOT NULL, item_hash VARCHAR(40) NOT NULL, UNIQUE INDEX UNIQ_E103DD1819882643 (item_hash), INDEX IDX_E103DD188BAC62AF (city_id), INDEX IDX_E103DD18A76ED395 (user_id), INDEX IDX_E103DD18913AEA17 (announcement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE announcement_stats_element ADD CONSTRAINT FK_E103DD18913AEA17 FOREIGN KEY (announcement_id) REFERENCES announcement (id)');
    }

    public function down(Schema $schema)
    {

    }
}
