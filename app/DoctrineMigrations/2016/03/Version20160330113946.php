<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160330113946 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE INDEX IDX_428D797316FE72E1 ON demand (updated_by)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
