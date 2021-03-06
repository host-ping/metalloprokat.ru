<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140219153551 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE stats_announcement (
                        id INT AUTO_INCREMENT NOT NULL,
                        announcement_id INT DEFAULT NULL,
                        date DATE NOT NULL,
                        redirects_count SMALLINT DEFAULT '0' NOT NULL,
                        displays_count SMALLINT DEFAULT '0' NOT NULL,
                        INDEX IDX_C6D8A7AB913AEA17 (announcement_id),
                        UNIQUE INDEX UNIQ_date_announcement (date, announcement_id),
                        PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
");
        $this->addSql("ALTER TABLE stats_announcement ADD CONSTRAINT FK_C6D8A7AB913AEA17 FOREIGN KEY (announcement_id) REFERENCES announcement (id);");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
