<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150210142031 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
        CREATE TABLE category_extended (
          id INT NOT NULL,
          category_id INT NOT NULL,
          pattern VARCHAR(1000) NOT NULL,
          extended_pattern VARCHAR(1000) NOT NULL,
          test_pattern VARCHAR(1000) NOT NULL,
          matching_priority INT DEFAULT 0 NOT NULL,
          description VARCHAR(2000) NOT NULL,
          UNIQUE INDEX UNIQ_92C7050412469DE2 (category_id),
          PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");

        $this->addSql(
            "ALTER TABLE category_extended
              ADD CONSTRAINT FK_92C7050412469DE2 FOREIGN KEY (category_id) REFERENCES Message73 (Message_ID) ON DELETE CASCADE"
        );

        $this->addSql("
            INSERT INTO category_extended (id, category_id, pattern, extended_pattern, test_pattern, matching_priority, description)
              SELECT Message_ID, Message_ID, pattern, extended_pattern, test_pattern, matching_priority, description FROM Message73
        ");

        $this->addSql("
            ALTER TABLE Message73
                DROP pattern,
                DROP test_pattern,
                DROP matching_priority,
                DROP extended_pattern,
                DROP description,
                ADD category_extended_id INT DEFAULT NULL,
                ADD CONSTRAINT FK_6281B256193FCE28 FOREIGN KEY (category_extended_id) REFERENCES category_extended (id) ON DELETE SET NULL
        ");

        $this->addSql("CREATE UNIQUE INDEX UNIQ_6281B256193FCE28 ON Message73 (category_extended_id)");

        $this->addSql("UPDATE Message73 SET category_extended_id = Message_ID;");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
