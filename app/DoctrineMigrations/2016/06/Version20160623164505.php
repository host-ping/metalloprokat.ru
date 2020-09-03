<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160623164505 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE grabber_parsed_demand DROP FOREIGN KEY FK_B85798745D022E59;");
        $this->addSql("ALTER TABLE grabber_parsed_demand ADD CONSTRAINT FK_FD879E995D022E59 FOREIGN KEY (demand_id) REFERENCES demand (id) ON DELETE CASCADE");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
