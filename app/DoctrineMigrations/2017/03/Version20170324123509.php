<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170324123509 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE demand_file (id INT AUTO_INCREMENT NOT NULL, demand_id INT NOT NULL, name VARCHAR(255) NOT NULL, original_name VARCHAR(255) NOT NULL, mime_type VARCHAR(255) NOT NULL, size INT NOT NULL, INDEX IDX_525D96565D022E59 (demand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE demand_file ADD CONSTRAINT FK_525D96565D022E59 FOREIGN KEY (demand_id) REFERENCES demand (id) ON DELETE CASCADE');
        $this->addSql('
            INSERT INTO demand_file (demand_id, name, original_name, mime_type, size)
            SELECT id, file_path, file_original_name, file_mime, file_size FROM demand WHERE (file_size IS NOT NULL AND file_size != 0)    
            '
        );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
