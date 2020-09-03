<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150810123846 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('
          CREATE TABLE stats_product_change (
              date_created_at DATE NOT NULL,
              product_id INT NOT NULL,
              company_id INT NOT NULL,
              id INT NOT NULL,
              is_added TINYINT(1) NOT NULL,
              INDEX IDX_3E1250A14584665A (product_id),
              INDEX IDX_3E1250A1979B1AD6 (company_id),
              PRIMARY KEY(date_created_at, product_id))
              DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
    }

    public function down(Schema $schema)
    {
    }
}
