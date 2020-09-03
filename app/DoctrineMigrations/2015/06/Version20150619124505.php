<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150619124505 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Classificator_Region SET Keyword = null WHERE Keyword = ''");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
