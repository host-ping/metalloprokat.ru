<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150325142930 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE grabber_site SET host = 'http://metallorus.ru' WHERE slug = 'metallorus'");
        $this->addSql("UPDATE grabber_parsed_demand SET url = replace(url, 'ru//', 'ru/')");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
