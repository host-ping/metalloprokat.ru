<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150720133951 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE stats_city ADD show_products_count SMALLINT DEFAULT '0' NOT NULL;");
        $this->addSql("ALTER TABLE stats_daily ADD show_products_count SMALLINT DEFAULT '0' NOT NULL;");
        $this->addSql("ALTER TABLE stats_category ADD show_products_count SMALLINT DEFAULT '0' NOT NULL;");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
