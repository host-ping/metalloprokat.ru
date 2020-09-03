<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150924174040 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
            CREATE TABLE catalog_review (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, user_id INT DEFAULT NULL,
            city_id INT NOT NULL, deleted_by INT DEFAULT NULL, moderated_by INT DEFAULT NULL, type TINYINT(1) NOT NULL,
            created_at DATETIME NOT NULL, comment VARCHAR(255) NOT NULL, mail VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL,
            deleted_at DATETIME DEFAULT NULL, moderated_at DATETIME DEFAULT NULL, Category_ID INT DEFAULT NULL, INDEX IDX_CB47998F4584665A (product_id),
            INDEX IDX_CB47998FA76ED395 (user_id), INDEX IDX_CB47998F8BAC62AF (city_id), INDEX IDX_CB47998F1F6FA0AF (deleted_by),
            INDEX IDX_CB47998F3A30165F (Category_ID), INDEX IDX_CB47998F6F9F06A4 (moderated_by), INDEX IDX_product_deleted_by (product_id, deleted_by),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;"
        );

        $this->addSql("CREATE INDEX IDX_44DCE5B33A30165F ON company_review (Category_ID);");
        $this->addSql("CREATE INDEX IDX_44DCE5B36F9F06A4 ON company_review (moderated_by);");
        $this->addSql("ALTER TABLE catalog_review ADD CONSTRAINT FK_CB47998F4584665A FOREIGN KEY (product_id) REFERENCES catalog_product (id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
