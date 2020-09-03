<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161102101258 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE instagram_account_tag_comment ADD instagram_account_location_id INT DEFAULT NULL');

        $this->addSql('ALTER TABLE instagram_account_tag_comment
  ADD CONSTRAINT FK_3B95ACFF9B9DF2B7 FOREIGN KEY (instagram_account_location_id) REFERENCES instagram_account_location (id)');

        $this->addSql('CREATE INDEX IDX_3B95ACFF9B9DF2B7
  ON instagram_account_tag_comment (instagram_account_location_id)');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
