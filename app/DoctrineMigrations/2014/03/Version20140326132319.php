<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140326132319 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE stats_announcement DROP FOREIGN KEY FK_C6D8A7AB913AEA17');
        $this->addSql('ALTER TABLE stats_announcement ADD CONSTRAINT FK_C6D8A7AB913AEA17 FOREIGN KEY (announcement_id) REFERENCES announcement (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE announcement_stats_element DROP FOREIGN KEY FK_E103DD18913AEA17');
        $this->addSql('ALTER TABLE announcement_stats_element ADD CONSTRAINT FK_E103DD18913AEA17 FOREIGN KEY (announcement_id) REFERENCES announcement (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
