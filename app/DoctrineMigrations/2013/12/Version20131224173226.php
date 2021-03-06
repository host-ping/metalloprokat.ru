<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20131224173226 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE user_visiting (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL,
                        date DATE NOT NULL, INDEX IDX_63D9B5ADA76ED395 (user_id), UNIQUE INDEX UNIQ_user_date (user_id, date),
                         PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
');
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
