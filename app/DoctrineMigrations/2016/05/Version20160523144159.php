<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160523144159 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Payments CHANGE Company_ID Company_ID INT DEFAULT NULL, CHANGE service_order_id package_order_id INT DEFAULT NULL');

        $this->addSql('ALTER TABLE company_registration CHANGE package package_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_9C656D69F44CABFF ON company_registration (package_id)');

        $this->addSql('ALTER TABLE service_packages CHANGE package_type_id package_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_62C39B9AF44CABFF ON service_packages (package_id)');
        // этот апдейт для того, чтобы была консистентность в методах getPackagePeriodName и getSimpleTermPackage
        $this->addSql('UPDATE service_packages SET package_period = package_period + 1');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
