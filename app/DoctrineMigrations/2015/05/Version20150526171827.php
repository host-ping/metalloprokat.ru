<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150526171827 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Message155 SET slug = REPLACE(slug, '_', '-') WHERE slug LIKE '%\_%'");
        $this->addSql("UPDATE attribute_value SET slug = REPLACE(slug, '_', '-') WHERE slug LIKE '%\_%'");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
