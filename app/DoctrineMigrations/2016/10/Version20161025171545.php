<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161025171545 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE company_delivery_city ADD display_position SMALLINT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE User ADD display_position SMALLINT DEFAULT 1 NOT NULL');
        $this->addSql('UPDATE company_delivery_city cdc
            JOIN (
                SELECT MAX(cdcmax.display_priority) AS max_display_priority, cdcmax.company_id FROM company_delivery_city cdcmax GROUP BY cdcmax.company_id
                ) sq
              ON sq.company_id = cdc.company_id
            SET cdc.display_position = sq.max_display_priority - cdc.display_priority + 1;'
        );


        $this->addSql('UPDATE User u
            JOIN (
                SELECT MAX(umax.display_priority) AS max_display_priority, umax.ConnectCompany FROM User umax GROUP BY umax.ConnectCompany
                ) sq
              ON sq.ConnectCompany = u.ConnectCompany
            SET u.display_position = sq.max_display_priority - u.display_priority + 1;'
        );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
