<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160329121845 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE `Message179` ADD `type4` TINYINT( 4 ) NOT NULL DEFAULT '0' AFTER `type3` ;");
        $this->addSql("ALTER TABLE `Message179` ADD `type1Val` VARCHAR( 100 ) NULL ,
                        ADD `type2Val` VARCHAR( 100 ) NULL ,
                        ADD `type3Val` VARCHAR( 100 ) NULL ,
                        ADD `type4Val` VARCHAR( 100 ) NULL ;
                       ");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
