<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160323110352 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE instagram_stats
                     ADD user_id INT NOT NULL,
                     ADD description VARCHAR(255) DEFAULT NULL,
                     ADD tags LONGTEXT NOT NULL COMMENT '(DC2Type:array)',
                     ADD instagram_created_at DATETIME NOT NULL;"
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
