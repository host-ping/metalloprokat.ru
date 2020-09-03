<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170614085542 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE demand_item SET title = TRIM(CHAR(9) FROM TRIM(REPLACE(TRIM(title), '\t', ' ')))");
        $this->addSql("UPDATE demand SET info = TRIM(CHAR(9) FROM TRIM(REPLACE(TRIM(info), '\t', ' ')))");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
