<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150928110840 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE UNIQUE INDEX UNIQ_7C786E0677153098 ON promocode (code)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
