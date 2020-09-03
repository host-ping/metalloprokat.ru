<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150420120707 extends AbstractMigration
{

    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Message75 SET title = '' WHERE slug LIKE '-%'");
        $this->addSql("
            UPDATE
            Message75 as company
            JOIN (
                 SELECT c.Message_ID, country.base_host
                 FROM Message75 AS c JOIN Classificator_Country AS country ON c.country_id = country.Country_ID
                 WHERE c.Message_ID IN (SELECT c1.Message_ID FROM Message75 AS c1 WHERE slug LIKE '-%')
              ) AS sub on company.Message_ID = sub.Message_ID
           SET company.slug = concat('company-', sub.Message_ID),
              company.domain = concat('company-', sub.Message_ID, '.', sub.base_host)
        ");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
