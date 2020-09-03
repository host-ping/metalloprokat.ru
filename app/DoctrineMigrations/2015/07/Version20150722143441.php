<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150722143441 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE company_delivery_city cd
            JOIN Message75 com ON com.company_city = cd.city_id AND com.Message_ID = cd.company_id
            SET cd.is_main_office = 1;
        ');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
