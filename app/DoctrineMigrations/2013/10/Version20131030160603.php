<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131030160603 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('DROP INDEX IDX_5F2732B5232D562B ON complaint;');
        $this->addSql('ALTER TABLE complaint ADD company_id INT DEFAULT NULL, ADD product_id INT DEFAULT NULL, ADD processed_at DATETIME DEFAULT NULL, DROP object_id, CHANGE complaint_object_type complaint_object_type VARCHAR(255) NOT NULL;');
        $this->addSql('CREATE INDEX IDX_5F2732B5979B1AD6 ON complaint (company_id);');
        $this->addSql('CREATE INDEX IDX_5F2732B54584665A ON complaint (product_id);');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
