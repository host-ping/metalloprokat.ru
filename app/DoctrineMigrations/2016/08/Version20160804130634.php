<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160804130634 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE user_auto_login (
            id INT AUTO_INCREMENT NOT NULL,
            user_id INT NOT NULL, 
            token VARCHAR(40) NOT NULL, 
            created_at DATETIME NOT NULL, 
            authentications_count SMALLINT DEFAULT '0' NOT NULL, 
            INDEX IDX_4EA6207A76ED395 (user_id), PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
        ");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
