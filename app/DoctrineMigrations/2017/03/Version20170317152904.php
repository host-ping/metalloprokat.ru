<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170317152904 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE content_category SET parent_id = NULL WHERE parent_id = 0');
        $this->addSql('UPDATE custom_company_category SET parent_id = NULL WHERE parent_id = 0');
        $this->addSql('UPDATE Message73 SET cat_parent = NULL WHERE cat_parent = 0');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
