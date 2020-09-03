<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131118110747 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE company_attribute DROP INDEX UNIQ_C961650B979B1AD6, ADD INDEX IDX_C961650B979B1AD6 (company_id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_company ON company_attribute (company_id, type)");

    }

    public function down(Schema $schema)
    {
    }
}
