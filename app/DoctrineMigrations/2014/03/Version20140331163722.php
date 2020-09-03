<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140331163722 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE demand ADD possible_user_id INT DEFAULT NULL, ADD RegistrationCode VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_428D797362334C12 ON demand (possible_user_id)');

    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
