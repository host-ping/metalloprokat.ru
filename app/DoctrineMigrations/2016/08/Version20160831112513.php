<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160831112513 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // создаем таблицу где будем хранить id скопированных на строй заявок
        $this->addSql('CREATE TABLE demand_mirror (
            demand_id INT NOT NULL, 
            original_demand_id INT NOT NULL, 
            mirrored_at DATETIME NOT NULL, PRIMARY KEY(demand_id)
            ) 
            DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB
            '
        );
        $this->addSql('ALTER TABLE demand_mirror ADD CONSTRAINT FK_E73316C85D022E59 FOREIGN KEY (demand_id) REFERENCES demand (id)');
        // колонка для пометки заявок на копирование на строй
        $this->addSql("ALTER TABLE demand ADD mirror_to_stroy TINYINT(1) DEFAULT '0' NOT NULL");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
