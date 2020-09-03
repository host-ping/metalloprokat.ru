<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151111184436 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE catalog_product ADD created_by INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_DCF8F981DE12AB56 ON catalog_product (created_by)');
        // id Дениса
        $this->addSql('UPDATE catalog_product SET created_by = 125395');


    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
