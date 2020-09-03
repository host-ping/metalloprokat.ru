<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140304073843 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE mini_site_cover DROP INDEX IDX_4B18D342979B1AD6, ADD UNIQUE INDEX UNIQ_4B18D342979B1AD6 (company_id)');
        $this->addSql('ALTER TABLE company_minisite ADD CONSTRAINT FK_C6AEDBB6922726E9 FOREIGN KEY (cover_id) REFERENCES mini_site_cover (id)');
        $this->addSql('CREATE INDEX IDX_C6AEDBB6922726E9 ON company_minisite (cover_id)');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
