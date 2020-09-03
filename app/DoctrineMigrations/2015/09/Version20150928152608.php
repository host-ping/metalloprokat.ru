<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150928152608 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Message75 ADD promocode_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE Message75 ADD CONSTRAINT FK_8BE21763C76C06D9 FOREIGN KEY (promocode_id) REFERENCES promocode (id)");
        $this->addSql("CREATE INDEX IDX_8BE21763C76C06D9 ON Message75 (promocode_id)");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
