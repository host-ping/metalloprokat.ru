<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160804150302 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE user_auto_login CHANGE token token CHAR(40) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4EA62075F37A13B ON user_auto_login (token)');
        $this->addSql("ALTER TABLE user_registration_with_parser CHANGE notify notified TINYINT(1) DEFAULT '0' NOT NULL");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
