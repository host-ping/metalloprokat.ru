<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141106141211 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("UPDATE User SET additional_role_id = 1 WHERE Email IN ('d.arzhanov@metalloprokat.ru', 'serglebedev10@mail.ru')");
        $this->addSql("UPDATE User SET additional_role_id = 2 WHERE User_ID IN (51795, 51797, 51813, 6395)");
        $this->addSql("UPDATE User SET additional_role_id = 9 WHERE Email IN ('koc-dp@yandex.ru', 'tashka7@gmail.com')");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
