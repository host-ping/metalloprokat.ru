<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150527172434 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE user_city ADD country_id INT DEFAULT NULL, CHANGE city_id city_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_57DA4EFDF92F3E70 ON user_city (country_id)');
        $this->addSql('ALTER TABLE Classificator_Country ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_city ADD CONSTRAINT FK_57DA4EFDF92F3E70 FOREIGN KEY (country_id) REFERENCES Classificator_Country (Country_ID) ON DELETE CASCADE;');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
