<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150724161217 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('
            UPDATE UserSend AS us
            JOIN User AS u ON us.user_id = u.User_ID
            JOIN Message75 AS c ON u.ConnectCompany = c.Message_ID
            SET us.deleted = TRUE
            WHERE c.deleted_at_ts != 0
        ');
        $this->addSql('
            UPDATE UserSend AS us
            JOIN User AS u ON us.user_id = u.User_ID
            SET us.deleted = TRUE
            WHERE u.Checked = FALSE
        ');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
