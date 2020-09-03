<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131114115927 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE company_attribute (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, has_production TINYINT(1) NOT NULL, has_cutting TINYINT(1) NOT NULL, has_bending TINYINT(1) NOT NULL, has_delivery TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_C961650B979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql("CREATE INDEX IDX_79F97426979B1AD6 ON callback (company_id)");
    }

    public function down(Schema $schema)
    {

    }
}
