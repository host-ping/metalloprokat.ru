<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141225130412 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('ALTER TABLE `Message75` ADD `spros_end` DATE NULL ;');
        $this->addSql('DELETE FROM User_spros WHERE Balance < 1 ;');
        $this->addSql('UPDATE `Message75` ,
            User_spros us
            SET `spros_end` = date_add( now( ) , INTERVAL us.Balance / us.Abon DAY )
            WHERE us.User_ID = Message_ID');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
