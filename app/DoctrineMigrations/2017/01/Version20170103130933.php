<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170103130933 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            'CREATE TABLE demand_item_attribute_value 
            (
                id INT AUTO_INCREMENT NOT NULL, demand_id INT NOT NULL, demand_item_id INT NOT NULL, 
                attribute_value_id INT NOT NULL, INDEX IDX_8F81F3735D022E59 (demand_id), 
                INDEX IDX_8F81F373BD15BC87 (demand_item_id), INDEX IDX_8F81F37365A22152 (attribute_value_id),
                PRIMARY KEY(id)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;'
        );

        $this->addSql('ALTER TABLE demand_item_attribute_value ADD CONSTRAINT FK_8F81F3735D022E59 FOREIGN KEY (demand_id) REFERENCES demand (id)');
        $this->addSql('ALTER TABLE demand_item_attribute_value ADD CONSTRAINT FK_8F81F373BD15BC87 FOREIGN KEY (demand_item_id) REFERENCES demand_item (id)');
        $this->addSql('ALTER TABLE demand_item_attribute_value ADD CONSTRAINT FK_8F81F37365A22152 FOREIGN KEY (attribute_value_id) REFERENCES attribute_value (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
