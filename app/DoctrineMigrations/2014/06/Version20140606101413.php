<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140606101413 extends AbstractMigration
{
    public function up(Schema $schema)
    {

        $this->addSql(' DELETE FROM url_rewrite WHERE company_id IN (
                            SELECT c.Message_ID FROM Message75 AS c WHERE c.deleted_at_ts > 0
                        );
                    ');
        // this up() migration is autogenerated, please modify it to your needs

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
