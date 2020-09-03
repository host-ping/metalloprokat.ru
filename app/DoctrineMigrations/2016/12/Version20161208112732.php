<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161208112732 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE landing_page ADD breadcrumb_category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE landing_page ADD CONSTRAINT FK_87A7C89973E6102F FOREIGN KEY (breadcrumb_category_id) REFERENCES Message73 (Message_ID) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_87A7C89973E6102F ON landing_page (breadcrumb_category_id)');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
