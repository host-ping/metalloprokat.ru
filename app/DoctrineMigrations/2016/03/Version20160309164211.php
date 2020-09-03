<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160309164211 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Message179 AS serviceItem SET serviceItem.link_pattern = 'www.%base_host%/price' WHERE serviceItem.Message_ID = 9");
        $this->addSql("UPDATE Message179 AS serviceItem SET serviceItem.link_pattern = 'www.%base_host%/demands' WHERE serviceItem.Message_ID = 16");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
