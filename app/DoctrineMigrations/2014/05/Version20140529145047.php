<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20140529145047 extends AbstractMigration
{
    public function up(Schema $schema)
    {

        $this->addSql('
            DROP TABLE IF EXISTS related_categories;
            DROP TABLE IF EXISTS related_category;
        ');
        $this->addSql(
            'CREATE TABLE related_category (id INT AUTO_INCREMENT NOT NULL, related_category INT DEFAULT NULL, category INT NOT NULL, INDEX IDX_B03E4ED8B03E4ED8 (related_category), UNIQUE INDEX UNIQ_related_category (category, related_category), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;'
        );
        $this->addSql('ALTER TABLE related_category ADD CONSTRAINT FK_B03E4ED8B03E4ED8 FOREIGN KEY (related_category) REFERENCES Message73 (Message_ID);');
        $this->addSql('
                        INSERT INTO related_category (category, related_category) VALUES (29, 484);
                        INSERT INTO related_category (category, related_category) VALUES (30, 493);
                        INSERT INTO related_category (category, related_category) VALUES (30, 627);
                        INSERT INTO related_category (category, related_category) VALUES (100, 992);
                        INSERT INTO related_category (category, related_category) VALUES (39, 585);
                        INSERT INTO related_category (category, related_category) VALUES (40, 504);
                        INSERT INTO related_category (category, related_category) VALUES (32, 505);
                        INSERT INTO related_category (category, related_category) VALUES (38, 1059);
                        INSERT INTO related_category (category, related_category) VALUES (57, 506);
                        INSERT INTO related_category (category, related_category) VALUES (475, 53);
                        INSERT INTO related_category (category, related_category) VALUES (60, 458);
                        INSERT INTO related_category (category, related_category) VALUES (101, 588);
                        INSERT INTO related_category (category, related_category) VALUES (114, 589);
                        INSERT INTO related_category (category, related_category) VALUES (103, 590);
                        INSERT INTO related_category (category, related_category) VALUES (98, 591);
                        INSERT INTO related_category (category, related_category) VALUES (108, 592);
                        INSERT INTO related_category (category, related_category) VALUES (519, 593);
                        INSERT INTO related_category (category, related_category) VALUES (521, 595);
                        INSERT INTO related_category (category, related_category) VALUES (520, 595);
                        INSERT INTO related_category (category, related_category) VALUES (94, 596);
                        INSERT INTO related_category (category, related_category) VALUES (94, 95);
        ');
        $this->addSql('
                        INSERT INTO related_category (category, related_category) VALUES (120, 650);
                        INSERT INTO related_category (category, related_category) VALUES (112, 670);
                        INSERT INTO related_category (category, related_category) VALUES (112, 525);
                        INSERT INTO related_category (category, related_category) VALUES (97, 672);
                        INSERT INTO related_category (category, related_category) VALUES (111, 674);
                        INSERT INTO related_category (category, related_category) VALUES (106, 676);
                        INSERT INTO related_category (category, related_category) VALUES (113, 677);
                        INSERT INTO related_category (category, related_category) VALUES (110, 678);
                        INSERT INTO related_category (category, related_category) VALUES (107, 690);
                        INSERT INTO related_category (category, related_category) VALUES (123, 651);
                        INSERT INTO related_category (category, related_category) VALUES (118, 644);
                        INSERT INTO related_category (category, related_category) VALUES (124, 647);
                        INSERT INTO related_category (category, related_category) VALUES (16, 87);
                        INSERT INTO related_category (category, related_category) VALUES (131, 641);
                        INSERT INTO related_category (category, related_category) VALUES (532, 681);
                        INSERT INTO related_category (category, related_category) VALUES (102, 671);
                        INSERT INTO related_category (category, related_category) VALUES (109, 669);
                        INSERT INTO related_category (category, related_category) VALUES (92, 668);
                        INSERT INTO related_category (category, related_category) VALUES (91, 667);
                        INSERT INTO related_category (category, related_category) VALUES (90, 666);
                        INSERT INTO related_category (category, related_category) VALUES (89, 665);
                        INSERT INTO related_category (category, related_category) VALUES (1239, 1238);
                        INSERT INTO related_category (category, related_category) VALUES (586, 77);
        ');
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs

    }
}
