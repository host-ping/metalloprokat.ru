<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160330155810 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE ban_request ADD user_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_E3461AE6A76ED395 ON ban_request (user_id)');
        $this->addSql('ALTER TABLE ban_request ADD CONSTRAINT FK_E3461AE6A76ED395 FOREIGN KEY (user_id) REFERENCES User (User_ID)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
