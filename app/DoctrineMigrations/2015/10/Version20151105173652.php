<?php

namespace Application\Migrations;

use Behat\Transliterator\Transliterator;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\CategoriesBundle\Entity\ParameterOption;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151105173652 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("ALTER TABLE attribute_value ADD old_slug VARCHAR(100) DEFAULT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
