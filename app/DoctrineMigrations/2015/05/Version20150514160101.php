<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150514160101 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE UNIQUE INDEX UNIQ_email_date_send ON email_deferred (email, date_send)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
