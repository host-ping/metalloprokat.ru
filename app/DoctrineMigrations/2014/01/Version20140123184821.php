<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140123184821 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE url_rewrite (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, company_id INT DEFAULT NULL, path_prefix VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_B8985B8BF22F1BF5 (path_prefix), UNIQUE INDEX UNIQ_B8985B8B12469DE2 (category_id), UNIQUE INDEX UNIQ_B8985B8B979B1AD6 (company_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
