<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150805123209 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            '
            UPDATE Message142 AS product
              JOIN product_log AS pl ON pl.product_id = product.Message_ID
              JOIN User AS u ON pl.created_by = u.User_ID
              JOIN Message75 AS company ON company.Message_ID = u.ConnectCompany
            SET product.Company_ID = company.Message_ID
            WHERE product.Company_ID IS NULL
        '
        );

        $this->addSql('DELETE FROM stats_daily WHERE company_id = 0');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
