<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150522114520 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('
            UPDATE Message142 p
            JOIN Message75 com
              ON p.Company_ID = com.Message_ID
            JOIN Classificator_Country c
              ON com.country_id = c.Country_ID
            SET p.currency_id = c.currency_id
            WHERE p.currency_id = 0;
');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
