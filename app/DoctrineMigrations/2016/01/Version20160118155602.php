<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160118155602 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE UNIQUE INDEX UNIQ_company_city ON company_delivery_city (company_id, city_id)");
        $this->addSql("ALTER TABLE company_delivery_city DROP INDEX UNIQ_category_city");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
