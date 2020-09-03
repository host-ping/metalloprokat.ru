<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150825131811 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('
            INSERT INTO stats_product_change (id, date_created_at, product_id, company_id, is_added)
            SELECT id, date_created_at, product_id, company_id, is_added
            FROM stats_product_changes'
        );

        $this->addSql('
            DELETE spc
            FROM stats_product_change spc
            JOIN stats_product_change c ON spc.product_id = c.product_id AND spc.date_created_at = c.date_created_at AND c.is_added = 1
            WHERE spc.is_added = 0'
        );

        $this->addSql('
            DELETE spc
            FROM stats_product_change spc
            JOIN stats_product_change c ON spc.product_id = c.product_id AND spc.date_created_at = c.date_created_at AND spc.id <> c.id
            WHERE c.company_id = 0;'
        );

        $this->addSql('
            UPDATE stats_product_change spc
            JOIN Message142 p ON p.Message_ID = spc.product_id
            SET spc.company_id = p.Company_ID
            WHERE spc.company_id = 0'
        );

        $this->addSql('DELETE FROM stats_product_change WHERE company_id = 0');

        $this->addSql('ALTER TABLE stats_product_change DROP COLUMN id');

        $this->addSql('ALTER TABLE stats_product_change ADD PRIMARY KEY(date_created_at, product_id)');

        $this->addSql('DROP TABLE stats_product_changes');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
