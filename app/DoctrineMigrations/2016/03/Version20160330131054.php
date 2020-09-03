<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160330131054 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if ($this->container->getParameter('project.family') === 'stroy') {

            $this->addSql("UPDATE announcement_zone SET width = 960, height = 90, cost = 45000 WHERE slug = 'premium' ");
            $this->addSql("UPDATE announcement_zone SET width = 150, height = 610, cost = 35000 WHERE slug = 'left-sidebar' ");
            $this->addSql("UPDATE announcement_zone SET width = 150, height = 450, cost = 25000 WHERE slug = 'right-sidebar-1' ");
            $this->addSql("UPDATE announcement_zone SET width = 150, height = 150, cost = 15000 WHERE slug = 'right-sidebar-2' ");
            $this->addSql("UPDATE announcement_zone SET width = 150, height = 150, cost = 15000 WHERE slug = 'right-sidebar-3' ");

            $this->addSql("UPDATE announcement_zone SET cost = 17500 WHERE slug = 'products-content-1' ");
            $this->addSql("UPDATE announcement_zone SET cost = 15000 WHERE slug = 'products-content-2' ");
            $this->addSql("UPDATE announcement_zone SET cost = 12000 WHERE slug = 'products-content-3' ");
            $this->addSql("UPDATE announcement_zone SET cost = 9000 WHERE slug = 'products-content-4' ");
            $this->addSql("UPDATE announcement_zone SET cost = 6000 WHERE slug = 'products-content-5' ");
            $this->addSql("UPDATE announcement_zone SET cost = 4000 WHERE slug = 'products-content-6' ");
            $this->addSql("UPDATE announcement_zone SET cost = 4000 WHERE slug = 'products-content-7' ");
        }
        
        
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
