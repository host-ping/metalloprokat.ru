<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150902180453 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("DROP INDEX UNIQ_phone_company ON normalize_phones");
        $this->addSql("DROP INDEX UNIQ_phone_user ON normalize_phones");
        $this->addSql("ALTER TABLE normalize_phones ADD demand_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE normalize_phones ADD CONSTRAINT FK_1F2A81715D022E59 FOREIGN KEY (demand_id) REFERENCES demand (id) ON DELETE CASCADE");
        $this->addSql("CREATE INDEX IDX_1F2A81715D022E59 ON normalize_phones (demand_id)");
        $this->addSql("ALTER TABLE normalize_phones CHANGE phone phone BIGINT(64) NOT NULL");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
