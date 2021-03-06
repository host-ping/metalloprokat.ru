<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130425132141 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE demand_category (id INT AUTO_INCREMENT NOT NULL, demand_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_F9EC095F5D022E59 (demand_id), INDEX IDX_F9EC095F12469DE2 (category_id), UNIQUE INDEX UNIQ_demand_category (demand_id, category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demand_category ADD CONSTRAINT FK_F9EC095F5D022E59 FOREIGN KEY (demand_id) REFERENCES demand (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
