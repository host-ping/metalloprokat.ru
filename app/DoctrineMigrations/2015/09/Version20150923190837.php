<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150923190837 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('
            UPDATE company_delivery_city AS cdc
             SET cdc.enabled = FALSE
             WHERE cdc.is_main_office = 0 AND cdc.company_id IN (
                 SELECT company_id FROM (
                    SELECT _cdc.company_id FROM company_delivery_city AS _cdc
                     JOIN Message75 AS c
                     ON _cdc.company_id = c.Message_ID
                      WHERE c.code_access = 0
                      GROUP BY _cdc.company_id
                      HAVING COUNT(*) > 1
                 ) AS t
             )
        ');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
