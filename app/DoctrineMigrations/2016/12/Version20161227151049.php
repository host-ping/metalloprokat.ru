<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\ProductsBundle\Entity\Product;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161227151049 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            'UPDATE Message142 SET moderated_at = :yesterday WHERE Checked = :status_checked',
            array('status_checked' => Product::STATUS_CHECKED, 'yesterday' => new \DateTime('-1 day')),
            array('yesterday' => 'datetime')
        );

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
