<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151203160833 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE url_rewrite ADD content_category_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8985B8B416C3764 ON url_rewrite (content_category_id)');
        $this->addSql('ALTER TABLE url_rewrite ADD CONSTRAINT FK_B8985B8B416C3764 FOREIGN KEY (content_category_id) REFERENCES content_category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
