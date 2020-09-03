<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150306142357 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            "CREATE TABLE landing_template (
                  id                 INT          NOT NULL,
                  category_id        INT DEFAULT NULL,
                  mime_type          VARCHAR(30)  NOT NULL,
                  file_size          INT          NOT NULL,
                  file_path          VARCHAR(255) NOT NULL,
                  file_original_name VARCHAR(255) NOT NULL,
                  created_at         DATETIME     NOT NULL,
                  updated_at         DATETIME     NOT NULL,
                  UNIQUE INDEX UNIQ_72D3D7AE12469DE2 (category_id),
                  PRIMARY KEY (id)
                )
                  DEFAULT CHARACTER SET utf8
                  COLLATE utf8_unicode_ci
                  ENGINE = InnoDB"
        );

        $this->addSql(
            "ALTER TABLE landing_template ADD CONSTRAINT FK_72D3D7AE12469DE2 FOREIGN KEY (category_id) REFERENCES Message73 (Message_ID) ON DELETE SET NULL"
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
