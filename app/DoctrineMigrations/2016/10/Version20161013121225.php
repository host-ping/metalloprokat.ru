<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161013121225 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE client_review CHANGE `comment` `comment` VARCHAR(1000) NOT NULL');
        $this->addSql('ALTER TABLE client_review ADD CONSTRAINT FK_2CE5CA751F6FA0AF FOREIGN KEY (deleted_by) REFERENCES User (User_ID)');
        $this->addSql('ALTER TABLE client_review ADD CONSTRAINT FK_2CE5CA756F9F06A4 FOREIGN KEY (moderated_by) REFERENCES User (User_ID)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
