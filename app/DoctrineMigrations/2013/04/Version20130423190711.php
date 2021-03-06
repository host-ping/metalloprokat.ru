<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130423190711 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE demand ADD category_id INT DEFAULT NULL, DROP category');
        $this->addSql('CREATE INDEX IDX_428D79738BAC62AF ON demand (city_id)');
        $this->addSql('CREATE INDEX IDX_428D797312469DE2 ON demand (category_id)');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
