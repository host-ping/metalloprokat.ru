<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150630171342 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Message75 AS company SET deleted_at_ts = deleted_at_ts + CEILING(RAND() * 1000) WHERE deleted_at_ts = 1401813712");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
