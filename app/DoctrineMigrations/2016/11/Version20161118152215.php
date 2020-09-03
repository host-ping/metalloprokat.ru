<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161118152215 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE user_city ADD region_id INT DEFAULT NULL;");

        $this->addSql("ALTER TABLE user_city ADD CONSTRAINT FK_57DA4EFD98260155 FOREIGN KEY (region_id) 
            REFERENCES Classificator_Regions (Regions_ID) ON DELETE CASCADE;");

        $this->addSql("CREATE INDEX IDX_57DA4EFD98260155 ON user_city (region_id);");

        $this->addSql("CREATE UNIQUE INDEX UNIQ_user_region ON user_city (user_id, region_id)");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
