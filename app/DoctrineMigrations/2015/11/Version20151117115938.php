<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151117115938 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(
            "CREATE TABLE announcement_category (
              id INT AUTO_INCREMENT NOT NULL, announcement_id INT NOT NULL,
              category_id INT NOT NULL, INDEX IDX_7D019332913AEA17 (announcement_id),
              INDEX IDX_7D01933212469DE2 (category_id), UNIQUE INDEX UNIQ_announcement_category
              (announcement_id, category_id), PRIMARY KEY(id))
              DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;"
        );

        $this->addSql("ALTER TABLE announcement DROP category_id;");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
