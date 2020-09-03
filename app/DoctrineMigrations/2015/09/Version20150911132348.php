<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150911132348 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE Message75 SET company_categories_titles = TRIM(company_categories_titles)');
        $this->addSql('UPDATE Message75 SET company_delivery_titles = TRIM(company_delivery_titles)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
