<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;


class Version20130911144104 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE city_counter (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, demands_count INT NOT NULL, UNIQUE INDEX UNIQ_city (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

        $this->addSql('
          INSERT INTO city_counter (city_id, demands_count)
            SELECT NULL, COUNT(id) FROM demand
            WHERE moderated_at IS NOT NULL');

        $this->addSql('
            INSERT IGNORE INTO city_counter (city_id, demands_count)
            SELECT Region_ID, 0 FROM Classificator_Region');
    }

    public function down(Schema $schema)
    {
    }
}
