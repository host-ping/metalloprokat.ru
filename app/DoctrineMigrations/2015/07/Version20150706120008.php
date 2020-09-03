<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150706120008 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE INDEX IDX_date_created_at ON stats_element (date_created_at)");
        $this->addSql("CREATE INDEX IDX_date_created_at ON announcement_stats_element (date_created_at)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
