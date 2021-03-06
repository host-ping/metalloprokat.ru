<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131220164118 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE stats_city (id INT AUTO_INCREMENT NOT NULL, company_city INT DEFAULT NULL, company_id INT NOT NULL, date DATE NOT NULL, demands_count SMALLINT NOT NULL, demands_processed_count SMALLINT NOT NULL, callback_count SMALLINT NOT NULL, callbacks_processed_count SMALLINT NOT NULL, reviews_count SMALLINT NOT NULL, complaints_count SMALLINT NOT NULL, complaints_processed_count SMALLINT NOT NULL, INDEX IDX_6C376A746E3618DA (company_city), INDEX IDX_6C376A74979B1AD6 (company_id), UNIQUE INDEX UNIQ_comp_date_city (company_id, date, company_city), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
