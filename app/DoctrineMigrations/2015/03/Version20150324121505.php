<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150324121505 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            UPDATE demand AS d
                JOIN grabber_parsed_demand AS gpd ON gpd.demand_id = d.id
            SET d.parsed_demand_id = gpd.id
        ");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
