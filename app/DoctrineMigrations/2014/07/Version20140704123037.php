<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140704123037 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE Message76 ADD is_automatically_added TINYINT(1) NOT NULL");
        $this->addSql("CREATE INDEX IDX_is_automatically_added ON Message76 (is_automatically_added)");
        $this->addSql("UPDATE  Message76 AS cc
                          JOIN Message142 AS p ON p.Company_ID = cc.company_id AND cc.cat_id = p.Category_ID
                            SET is_automatically_added = TRUE
                        WHERE p.Checked = 1
                     ");

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
