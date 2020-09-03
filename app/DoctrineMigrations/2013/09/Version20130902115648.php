<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130902115648 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->addSql("CREATE TABLE category_closure (id INT AUTO_INCREMENT NOT NULL, ancestor INT NOT NULL, descendant INT NOT NULL, depth INT NOT NULL, INDEX IDX_8FDFCDAFB4465BB (ancestor), INDEX IDX_8FDFCDAF9A8FAD16 (descendant), INDEX IDX_1FFF20AC3438F8D0 (depth), UNIQUE INDEX IDX_5F68E90CC651E49A (ancestor, descendant), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql('ALTER TABLE Message73 ADD COLUMN new_parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Message73 ADD INDEX IDX_6281B25640E5CF1F (new_parent_id)');
        $this->addSql('
            INSERT INTO category_closure (ancestor, descendant, depth)
            SELECT Message_ID, Message_ID, 0 FROM Message73');
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}