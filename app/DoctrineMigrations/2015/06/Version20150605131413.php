<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150605131413 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE demand ADD product_country_id INT DEFAULT NULL");
        $this->addSql("CREATE INDEX IDX_428D79736A48615B ON demand (product_country_id)");
        $this->addSql('
          UPDATE demand d
          JOIN Classificator_Region city
            ON d.product_city_id = city.Region_ID
          SET d.product_country_id = city.country_id
        ');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
