<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150529155936 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Message75 ADD company_categories_titles VARCHAR(16000) DEFAULT NULL COMMENT '(DC2Type:csv)', ADD company_delivery_titles VARCHAR(16000) DEFAULT NULL COMMENT '(DC2Type:csv)'");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
