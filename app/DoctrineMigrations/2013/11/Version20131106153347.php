<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131106153347 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE company_phone (company_id INT DEFAULT NULL, branch_office_id INT DEFAULT NULL, Message_ID INT AUTO_INCREMENT NOT NULL, phone VARCHAR(50) NOT NULL, additional_code VARCHAR(50) NOT NULL, INDEX IDX_3BE35B8979B1AD6 (company_id), INDEX IDX_3BE35B8FD2AF2F7 (branch_office_id), PRIMARY KEY(Message_ID)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
    }

    public function down(Schema $schema)
    {
    }
}
