<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141212124746 extends AbstractMigration
{
    public function up(Schema $schema)
    {
       $this->addSql("UPDATE Message73 SET real_category = 516 WHERE Message_ID IN(1066, 1067)");
       $this->addSql("UPDATE Message73 SET real_category = 38 WHERE Message_ID = 1065");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
