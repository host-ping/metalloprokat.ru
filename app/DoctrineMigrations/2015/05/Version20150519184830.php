<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150519184830 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE UNIQUE INDEX UNIQ_country_currency_date ON exchange_rate (country_id, currency_id, updated_at)");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
