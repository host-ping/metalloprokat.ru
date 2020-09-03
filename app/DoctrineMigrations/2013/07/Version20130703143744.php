<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130703143744 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE site (id INT AUTO_INCREMENT NOT NULL, hostname VARCHAR(255) NOT NULL, yandex_code VARCHAR(255) DEFAULT NULL, yandex_site_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");
        $this->addSql('INSERT INTO site (hostname) VALUES ("www.metalspros.ru")');
        $this->addSql('
            INSERT INTO site (hostname)
            (
                SELECT concat(Keyword, ".metalspros.ru")
                FROM Classificator_Region
                WHERE Keyword IS NOT NULL AND Keyword <> ""
            )');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP TABLE IF EXISTS site');
    }
}
