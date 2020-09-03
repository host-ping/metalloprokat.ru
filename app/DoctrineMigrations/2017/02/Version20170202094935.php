<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170202094935 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE demand_item_attribute_value DROP FOREIGN KEY FK_8F81F3735D022E59');
        $this->addSql('DROP INDEX IDX_8F81F3735D022E59 ON demand_item_attribute_value');
        $this->addSql('ALTER TABLE demand_item_attribute_value DROP demand_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
