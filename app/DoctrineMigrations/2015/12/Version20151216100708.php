<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151216100708 extends AbstractMigration
{
    public function up(Schema $schema)
    {
       $this->addSql("CREATE UNIQUE INDEX UNIQ_company_email ON normalized_email (company_id, email)");
       $this->addSql("CREATE UNIQUE INDEX UNIQ_user_email ON normalized_email (user_id, email)");
       $this->addSql("CREATE UNIQUE INDEX UNIQ_demand_email ON normalized_email (demand_id, email)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
