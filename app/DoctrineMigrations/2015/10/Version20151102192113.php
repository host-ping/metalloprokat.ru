<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151102192113 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Message75 SET company_logo = '' WHERE (company_logo = 'no_Logo' OR company_logo = 'нет' OR company_logo = 'NULL')");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
