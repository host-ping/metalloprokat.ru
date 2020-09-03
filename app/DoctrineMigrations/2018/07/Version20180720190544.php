<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180720190544 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE demand_notification (id INT AUTO_INCREMENT NOT NULL, demand_id INT DEFAULT NULL, service SMALLINT NOT NULL, notified_at DATETIME NOT NULL, INDEX IDX_7DE810495D022E59 (demand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demand_notification ADD CONSTRAINT FK_7DE810495D022E59 FOREIGN KEY (demand_id) REFERENCES demand (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema)
    {
    }
}
