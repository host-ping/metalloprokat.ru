<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150731131520 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            "
                    ALTER TABLE company_delivery_city
                    CHANGE kind kind TINYINT(1) NOT NULL DEFAULT 0,
                    CHANGE adress adress VARCHAR(255) DEFAULT '' NOT NULL,
                    CHANGE address_new address_new VARCHAR(100) DEFAULT '' NOT NULL
                    "
        );

        $this->addSql(
            '
            DELETE FROM company_delivery_city WHERE NOT EXISTS(
              SELECT company.Message_ID FROM Message75 AS company WHERE company.Message_ID = company_id
            )
            '
        );

        $this->addSql(
            'ALTER TABLE company_delivery_city ADD CONSTRAINT FK_80E5E8D3979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID)'
        );

        $this->addSql(
            'ALTER TABLE company_delivery_city ADD CONSTRAINT FK_80E5E8D38BAC62AF FOREIGN KEY (city_id) REFERENCES Classificator_Region (Region_ID)'
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
