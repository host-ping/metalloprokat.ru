<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170324113500 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('DROP INDEX UNIQ_C6AEDBB6979B1AD6 ON company_minisite');
        $this->addSql('ALTER TABLE company_minisite DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE company_minisite DROP id');
        $this->addSql('ALTER TABLE company_minisite ADD PRIMARY KEY (company_id)');
        $this->addSql('ALTER TABLE Message75 ADD minisite_config_id INT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8BE21763DFCD3BB4 ON Message75 (minisite_config_id)');

        $this->addSql('
        DELETE FROM company_minisite WHERE NOT EXISTS(
            SELECT * FROM Message75 WHERE Message_ID = company_minisite.company_id
          )
        ');

        $this->addSql('ALTER TABLE Message75 ADD CONSTRAINT FK_8BE21763DFCD3BB4 FOREIGN KEY (minisite_config_id) REFERENCES company_minisite (company_id)');
        $this->addSql('ALTER TABLE company_minisite ADD CONSTRAINT FK_C6AEDBB6979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
