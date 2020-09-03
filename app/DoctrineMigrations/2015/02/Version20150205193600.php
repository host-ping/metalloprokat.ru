<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150205193600 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
        UPDATE company_log l
          JOIN User u ON u.ConnectCompany = l.company_id
            SET l.created_by = (
                SELECT MIN(user.User_ID) FROM User user WHERE user.ConnectCompany = l.company_id
            )
            WHERE l.created_by IN (SELECT u.User_ID FROM User u WHERE ConnectCompany IS NULL OR u.ConnectCompany != l.company_id)"
        );

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
