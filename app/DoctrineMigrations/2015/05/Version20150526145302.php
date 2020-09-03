<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150526145302 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE Message142 p
            SET p.Image_ID = NULL
            WHERE p.Image_ID NOT IN (SELECT i.ID FROM Companies_images i)
        ');
        $this->addSql('ALTER TABLE Companies_images ENGINE = InnoDB;');
        $this->addSql('ALTER TABLE Message142 ADD CONSTRAINT FK_D373210B6A394351 FOREIGN KEY (Image_ID) REFERENCES Companies_images (ID) ON DELETE SET NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
