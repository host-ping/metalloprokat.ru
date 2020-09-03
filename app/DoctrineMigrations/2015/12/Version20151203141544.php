<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151203141544 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE content_topic ADD CONSTRAINT FK_76838AC912469DE2 FOREIGN KEY (category_id) REFERENCES content_category (id)');
        $this->addSql('ALTER TABLE content_topic ADD CONSTRAINT FK_76838AC91F4A3B9E FOREIGN KEY (category_secondary_id) REFERENCES content_category (id)');
        $this->addSql('ALTER TABLE content_question ADD CONSTRAINT FK_E440A3A112469DE2 FOREIGN KEY (category_id) REFERENCES content_category (id)');
        $this->addSql('ALTER TABLE content_question ADD CONSTRAINT FK_E440A3A11F4A3B9E FOREIGN KEY (category_secondary_id) REFERENCES content_category (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
