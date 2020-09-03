<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151012184942 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE catalog_brand ADD manufacturer_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_A54198E1A23B42D ON catalog_brand (manufacturer_id)');
        $this->addSql('ALTER TABLE catalog_brand ADD CONSTRAINT FK_A54198E1A23B42D FOREIGN KEY (manufacturer_id) REFERENCES catalog_manufacturer (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
