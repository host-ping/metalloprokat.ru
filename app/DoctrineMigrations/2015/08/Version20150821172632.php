<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150821172632 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE catalog_brand (id INT AUTO_INCREMENT NOT NULL,
                          attribute_value_id INT NOT NULL,
                          description LONGTEXT NOT NULL,
                          INDEX IDX_A54198E165A22152 (attribute_value_id),
                          PRIMARY KEY(id))
                          DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB");

        $this->addSql("ALTER TABLE catalog_brand ADD CONSTRAINT FK_A54198E165A22152 FOREIGN KEY (attribute_value_id)
                          REFERENCES attribute_value (id);");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
