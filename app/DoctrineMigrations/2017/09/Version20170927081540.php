<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170927081540 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            "
                UPDATE User AS u
                  LEFT JOIN Message75 AS c ON c.Message_ID = u.ConnectCompany
                SET u.ConnectCompany = NULL
                WHERE u.ConnectCompany IS NOT NULL AND c.Message_ID IS NULL
        "
        );

        $this->addSql(
            "DELETE FROM company_counter WHERE NOT EXISTS(
        SELECT c.Message_ID FROM Message75 AS c WHERE c.Message_ID = company_counter.company_id)"
        );

        $this->addSql(
            "DELETE FROM stats_daily WHERE NOT EXISTS(
        SELECT c.Message_ID FROM Message75 AS c WHERE c.Message_ID = stats_daily.company_id)"
        );

        $this->addSql(
            "DELETE FROM stats_city WHERE NOT EXISTS(
        SELECT c.Message_ID FROM Message75 AS c WHERE c.Message_ID = stats_city.company_id)"
        );

        $this->addSql(
            "DELETE FROM stats_category WHERE NOT EXISTS(
        SELECT c.Message_ID FROM Message75 AS c WHERE c.Message_ID = stats_category.company_id)"
        );

        $this->addSql(
            "DELETE FROM company_payment_details WHERE NOT EXISTS(
        SELECT c.Message_ID FROM Message75 AS c WHERE c.Message_ID = company_payment_details.company_id)"
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
