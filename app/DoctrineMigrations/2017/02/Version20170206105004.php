<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170206105004 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE Message75 AS company 
                        SET company.last_visit_at = company.Created WHERE 
                        (company.last_visit_at = '0000-00-00 00:00:00' OR company.last_visit_at IS NULL) 
                        AND (company.Created <> '0000-00-00 00:00:00' AND company.Created IS NOT NULL)
        ");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
