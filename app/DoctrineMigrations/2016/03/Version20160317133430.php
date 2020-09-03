<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160317133430 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE company_minisite CHANGE google_analytics_id google_analytics_id VARCHAR(255) DEFAULT NULL, CHANGE yandex_metrika_id yandex_metrika_id VARCHAR(255) DEFAULT NULL;');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
