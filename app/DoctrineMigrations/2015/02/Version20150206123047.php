<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150206123047 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("SET foreign_key_checks = 0");
        $this->addSql("DROP TABLE Message178");
        $this->addSql("SET foreign_key_checks = 1");
        
        $this->addSql("ALTER TABLE support_topic DROP FOREIGN KEY FK_6FFAC8C3BDFC9701");
        $this->addSql("ALTER TABLE support_topic ADD CONSTRAINT FK_6FFAC8C3BDFC9701 FOREIGN KEY (last_answer_id) REFERENCES support_answer (id) ON DELETE SET NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
