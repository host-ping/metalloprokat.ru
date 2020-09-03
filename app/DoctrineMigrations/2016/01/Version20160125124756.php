<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160125124756 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE INDEX IDX_8BE2176332FD9503 ON Message75 (company_region)");
        $this->addSql("CREATE INDEX IDX_8BE21763BB03C552 ON Message75 (virtual_product_id)");
        $this->addSql("CREATE INDEX IDX_code_access ON Message75 (code_access)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
