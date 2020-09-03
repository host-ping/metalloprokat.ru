<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151208154354 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE content_user_category ADD updated_at DATETIME NOT NULL");
        $this->addSql("ALTER TABLE content_user_category ADD CONSTRAINT FK_BD1BDBD012469DE2 FOREIGN KEY (category_id) REFERENCES content_category (id)");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
