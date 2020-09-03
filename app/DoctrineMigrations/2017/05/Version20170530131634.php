<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170530131634 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            'CREATE TABLE company_package_territory (
                id INT AUTO_INCREMENT NOT NULL, 
                company_id INT NOT NULL, 
                territory_id INT NOT NULL,
                created_at DATETIME NOT NULL, 
                updated_at DATETIME DEFAULT NULL, 
                spros_ends_at DATE DEFAULT NULL,
                package_id SMALLINT NOT NULL,
                starts_at DATE DEFAULT NULL, 
                ends_at DATE DEFAULT NULL,
                INDEX IDX_439AF4AD979B1AD6 (company_id), 
                INDEX IDX_439AF4AD73F74AD4 (territory_id), 
                UNIQUE INDEX company_territory (company_id, territory_id), 
                PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
                '
        );
        $this->addSql('ALTER TABLE company_package_territory ADD CONSTRAINT FK_439AF4AD979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID)');
        $this->addSql('ALTER TABLE company_package_territory ADD CONSTRAINT FK_439AF4AD73F74AD4 FOREIGN KEY (territory_id) REFERENCES territorial_structure (id) ON DELETE CASCADE;');

        $this->addSql("ALTER TABLE Message75 ADD code_access_territory VARCHAR(5000) DEFAULT NULL COMMENT '(DC2Type:json_array)'");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
