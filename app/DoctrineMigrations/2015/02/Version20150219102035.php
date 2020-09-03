<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150219102035 extends AbstractMigration
{
    public function up(Schema $schema)
    {
       $this->addSql(
           "RENAME TABLE demand_subscription TO demand_subscription_category;"
       );
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
