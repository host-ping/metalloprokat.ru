<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131014142240 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE UNIQUE INDEX UNIQ_company_category ON company_category_counter (company_id, category_id)");
        $this->addSql("CREATE INDEX IDX_8BE2176335991C25 ON Message75 (Manager)");
        $this->addSql("CREATE INDEX IDX_D373210BC639EE63 ON Message142 (User_ID)");
    }

    public function down(Schema $schema)
    {
    }
}
