<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140120193253 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE company_payment_detail (id INT NOT NULL, company_id INT DEFAULT NULL, attachment VARCHAR(255) DEFAULT NULL, attachment_uploaded_at DATETIME DEFAULT NULL, attachment_approved_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_company (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Message75 ADD payment_detail_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8BE217639BF92C93 ON Message75 (payment_detail_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8BE21763FCEEF2E3 ON Message75 (counter_id)');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
