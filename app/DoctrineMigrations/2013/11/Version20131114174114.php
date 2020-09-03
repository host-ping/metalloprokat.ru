<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131114174114 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE category_attribute_counter (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, city_id INT DEFAULT NULL, attribute_id INT NOT NULL, is_available TINYINT(1) NOT NULL, INDEX IDX_A0EEB73C12469DE2 (category_id), INDEX IDX_A0EEB73C8BAC62AF (city_id), INDEX IDX_A0EEB73CB6E62EFA (attribute_id), UNIQUE INDEX UNIQ_category_city (category_id, city_id, attribute_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
    }

    public function down(Schema $schema)
    {
    }
}
