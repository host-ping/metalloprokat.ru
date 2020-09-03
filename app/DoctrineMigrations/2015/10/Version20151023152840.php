<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151023152840 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE company_log CHANGE created_by created_by INT DEFAULT NULL");
        $this->addSql("UPDATE company_log SET created_by = NULL WHERE created_by = 0");
        $this->addSql("ALTER TABLE company_log ADD CONSTRAINT FK_8BCCECADDE12AB56 FOREIGN KEY (created_by) REFERENCES User (User_ID)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
