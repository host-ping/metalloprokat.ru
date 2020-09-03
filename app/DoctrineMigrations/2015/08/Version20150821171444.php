<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150821171444 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE catalog_manufacturer (
                         id INT AUTO_INCREMENT NOT NULL,
                         attribute_value_id INT NOT NULL,
                         description LONGTEXT NOT NULL,
                         INDEX IDX_C75DC1265A22152 (attribute_value_id),
                         PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

        $this->addSql("ALTER TABLE catalog_manufacturer ADD CONSTRAINT FK_C75DC1265A22152 FOREIGN KEY (attribute_value_id) REFERENCES attribute_value (id);");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
