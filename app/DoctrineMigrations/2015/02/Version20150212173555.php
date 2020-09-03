<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150212173555 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            UPDATE UserSend us JOIN User u ON us.user_id = u.User_ID
                SET us.Created = u.Created
            WHERE us.Created = '0000-00-00'"
        );

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
