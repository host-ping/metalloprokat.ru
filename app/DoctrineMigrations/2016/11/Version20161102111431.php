<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161102111431 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE instagram_stats ADD instagram_account_location_id INT DEFAULT NULL');

        $this->addSql('ALTER TABLE instagram_stats
  ADD CONSTRAINT FK_62023B349B9DF2B7 FOREIGN KEY (instagram_account_location_id) REFERENCES instagram_account_location (id)');

        $this->addSql('CREATE INDEX IDX_62023B349B9DF2B7 ON instagram_stats (instagram_account_location_id)');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
