<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150417121827 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE user_city (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, city_id INT NOT NULL,
            is_excluded TINYINT(1) DEFAULT '0' NOT NULL, INDEX IDX_57DA4EFDA76ED395 (user_id), INDEX IDX_57DA4EFD8BAC62AF (city_id),
             UNIQUE INDEX UNIQ_user_city (user_id, city_id),
             PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;");
        $this->addSql('ALTER TABLE User ENGINE = InnoDB;');
        $this->addSql("ALTER TABLE user_city ADD CONSTRAINT FK_57DA4EFDA76ED395 FOREIGN KEY (user_id) REFERENCES `User` (User_ID) ON DELETE CASCADE;");
        $this->addSql('ALTER TABLE Classificator_Region ENGINE = InnoDB;');
        $this->addSql("ALTER TABLE user_city ADD CONSTRAINT FK_57DA4EFD8BAC62AF FOREIGN KEY (city_id) REFERENCES Classificator_Region (Region_ID) ON DELETE CASCADE;");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
