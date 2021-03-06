<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140602181003 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('INSERT IGNORE INTO Message75 (Message_ID, User_ID, Subdivision_ID, Sub_Class_ID, Priority, Keyword, Checked, TimeToDelete, TimeToUncheck, IP, UserAgent, Parent_Message_ID, Created, LastUpdated, LastUser_ID, LastIP, LastUserAgent, company_name, company_type, company_phone, company_email, company_buy_concerned, company_sell_concerned, company_about, company_adress, company_last_name, company_first_name, company_legal_adres, company_mail_adress, company_inn, company_kpp, company_ogrn, company_bank, company_bik, company_rs, company_cor_bill, company_okpo, company_okved, company_head, company_logo, company_region, company_city, StatusMod, CheckDate, archive, rating, company_url, ratingUpdated, ReplicationStatus, Replication_StatusMet, inCrm, Manager, company_rating, code_access, domain, slogan, slug, latitude, longitude, coordinates_updated_at, address_new, country_id, deleted_at, deleted_at_ts) SELECT *, NOW(), UNIX_TIMESTAMP(NOW()) FROM Message75_log');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
