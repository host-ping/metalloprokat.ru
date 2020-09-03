<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161108173738 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE instagram_account_tag_comment ADD instagram_account_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE instagram_account_tag_comment ADD CONSTRAINT FK_3B95ACFFA0E6FB4C FOREIGN KEY 
          (instagram_account_id) REFERENCES instagram_account (id)");
        $this->addSql("CREATE INDEX IDX_3B95ACFFA0E6FB4C ON instagram_account_tag_comment (instagram_account_id)");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
