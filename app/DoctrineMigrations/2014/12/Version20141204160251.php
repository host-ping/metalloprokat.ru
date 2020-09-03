<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\TerritorialBundle\Entity\Country;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141204160251 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('UPDATE Message75 SET domain_id = 0 WHERE country_id != :russia', array('russia' => Country::COUNTRY_ID_RUSSIA));

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
