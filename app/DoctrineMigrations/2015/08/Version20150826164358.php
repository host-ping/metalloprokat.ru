<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150826164358 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE city_code SET code = 351 WHERE city_id = 2000');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
