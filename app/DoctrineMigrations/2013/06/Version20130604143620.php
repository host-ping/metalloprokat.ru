<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130604143620 extends AbstractMigration
{
    public function up(Schema $schema)
    {
		$this->addSql("CREATE INDEX IDX_company_keyword ON Message75 (Keyword)");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}