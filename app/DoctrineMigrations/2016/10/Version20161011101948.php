<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161011101948 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if ($schema->getTable('sphinx_search_log')->hasColumn('query_hash')) {
            $this->addSql("ALTER TABLE sphinx_search_log DROP query_hash;");
        }

        $this->addSql("ALTER TABLE sphinx_search_log ADD query_hash BINARY(20) NOT NULL;");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
