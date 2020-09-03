<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150911145419 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Message75 SET company_categories_titles = NULL WHERE LENGTH(company_categories_titles) < 2");
        $this->addSql("UPDATE Message75 SET company_delivery_titles = NULL WHERE LENGTH(company_delivery_titles) < 2");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
