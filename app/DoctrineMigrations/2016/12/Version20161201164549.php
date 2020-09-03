<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161201164549 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE category_city_metadata (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, city_id INT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, h1_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(1000) DEFAULT NULL, meta_keywords VARCHAR(255) DEFAULT NULL, INDEX IDX_D417693912469DE2 (category_id), INDEX IDX_D41769398BAC62AF (city_id), UNIQUE INDEX UNIQ_category_city (category_id, city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_city_metadata ADD CONSTRAINT FK_D417693912469DE2 FOREIGN KEY (category_id) REFERENCES Message73 (Message_ID) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_city_metadata ADD CONSTRAINT FK_D41769398BAC62AF FOREIGN KEY (city_id) REFERENCES Classificator_Region (Region_ID) ON DELETE SET NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
