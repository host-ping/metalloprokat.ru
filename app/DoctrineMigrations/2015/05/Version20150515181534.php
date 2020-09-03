<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150515181534 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE exchange_rates (id INT NOT NULL, country_id INT DEFAULT NULL, currency_id SMALLINT DEFAULT NULL,
        updated_at DATETIME NOT NULL, course NUMERIC(10, 0) NOT NULL, INDEX IDX_5AE3E774F92F3E70 (country_id),
        PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");

        $this->addSql("ALTER TABLE Classificator_Country CHANGE money_id currency_id SMALLINT DEFAULT NULL");
        $this->addSql("ALTER TABLE Message142 CHANGE Cur_ID currency_id SMALLINT NOT NULL, ADD normalized_price NUMERIC(10, 2) DEFAULT NULL");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
