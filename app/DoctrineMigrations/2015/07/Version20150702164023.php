<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150702164023 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('DELETE FROM company_delivery_city WHERE city_id IS NULL');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
