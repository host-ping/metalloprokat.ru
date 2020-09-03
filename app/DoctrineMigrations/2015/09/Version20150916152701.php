<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150916152701 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE catalog_product CHANGE Category_ID category_id INT NOT NULL;');
        $this->addSql('DROP INDEX IDX_D34A04AD3A30165F ON catalog_product');
        $this->addSql('ALTER TABLE catalog_product ENGINE = InnoDB');
        $this->addSql('CREATE INDEX IDX_DCF8F98112469DE2 ON catalog_product (category_id)');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
