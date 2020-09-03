<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150218164803 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            "INSERT IGNORE INTO demand_subscription (user_id, category_id)
                SELECT user.User_ID, companyCategory.cat_id FROM Message76 AS companyCategory
                  JOIN User AS user ON companyCategory.company_id = user.ConnectCompany
                WHERE EXISTS (
                  SELECT * FROM demand_subscription AS demandDescription WHERE demandDescription.user_id = user.User_ID
                )"
        );

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
