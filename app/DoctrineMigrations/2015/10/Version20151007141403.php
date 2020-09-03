<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151007141403 extends AbstractMigration
{
    public function up(Schema $schema)
    {
       $this->addSql("ALTER TABLE demand_view ADD company_id INT DEFAULT NULL AFTER user_id, CHANGE user_id user_id INT NOT NULL");
       $this->addSql("CREATE INDEX IDX_203F0BC8979B1AD6 ON demand_view (company_id)");
       $this->addSql("
                    UPDATE demand_view AS dv
                    JOIN User AS u ON dv.user_id = u.User_ID
                    JOIN Message75 AS company ON u.ConnectCompany = company.Message_ID
                    SET dv.company_id = company.Message_ID
                    ");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
