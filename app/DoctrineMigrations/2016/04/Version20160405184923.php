<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\ServicesBundle\Entity\Package;
use Metal\ServicesBundle\Entity\PackageOrder;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160405184923 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('ALTER TABLE Message106 ADD package_id TINYINT(1) NOT NULL');

        $this->addSql('
            UPDATE Message106 SET package_id = :status_full
            WHERE Special = 1
            ', array('status_full' => Package::FULL_PACKAGE)
        );

        $this->addSql('
            UPDATE Message106 SET package_id = :status_advanced
            WHERE Special = 0
            ', array('status_advanced' => Package::ADVANCED_PACKAGE)
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
