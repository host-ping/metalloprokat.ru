<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170330095733 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {

        $this->addSql('ALTER TABLE manufacturer_category DROP FOREIGN KEY FK_83948957A23B42D');
        $this->addSql('ALTER TABLE brand_category DROP FOREIGN KEY FK_C17C8AD044F5D008');

        $this->addSql('ALTER TABLE manufacturer_category ADD CONSTRAINT FK_83948957A23B42D FOREIGN KEY (manufacturer_id) REFERENCES catalog_manufacturer (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE brand_category ADD CONSTRAINT FK_C17C8AD044F5D008 FOREIGN KEY (brand_id) REFERENCES catalog_brand (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE brand_city DROP FOREIGN KEY FK_A3C088B44F5D008');
        $this->addSql('ALTER TABLE brand_city ADD CONSTRAINT FK_A3C088B44F5D008 FOREIGN KEY (brand_id) REFERENCES catalog_brand (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE manufacturer_city DROP FOREIGN KEY FK_A81033FBA23B42D');
        $this->addSql('ALTER TABLE manufacturer_city ADD CONSTRAINT FK_A81033FBA23B42D FOREIGN KEY (manufacturer_id) REFERENCES catalog_manufacturer (id) ON DELETE CASCADE');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
