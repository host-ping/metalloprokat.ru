<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150414170407 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE User SET approved_at = NULL WHERE approved_at IS NOT NULL AND ConnectCompany IS NULL");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
