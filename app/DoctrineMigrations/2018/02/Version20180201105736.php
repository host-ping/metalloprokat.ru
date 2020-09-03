<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180201105736 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE company_payment_details CHANGE okpo okpo VARCHAR(20) DEFAULT NULL, CHANGE bank_bik bank_bik VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE company_payment_details ADD CONSTRAINT FK_249E3F1D979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
