<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151127142712 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('DROP INDEX IDX_B2EB3A8912469DE2 ON content_answer;');
        $this->addSql('ALTER TABLE content_answer DROP category_id;');

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
