<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150904165920 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        if ($this->container->getParameter('project.family') === 'metalloprokat') {
            $this->addSql("UPDATE menu_item SET logo = 'ferrous-metallurgy.png' WHERE id = 1062");
            $this->addSql("UPDATE menu_item SET logo = 'non-ferrous-metallurgy.png' WHERE id = 1066");
            $this->addSql("UPDATE menu_item SET logo = 'semis.png' WHERE id = 1070");
            $this->addSql("UPDATE menu_item SET logo = 'equipment.png' WHERE id = 1071");
            $this->addSql("UPDATE menu_item SET logo = 'building-materials.png' WHERE id = 1072");
        }

        if ($this->container->getParameter('project.family') === 'product') {
            $this->addSql("UPDATE menu_item SET logo = 'food-items.png' WHERE id = 1359");
            $this->addSql("UPDATE menu_item SET logo = 'agricultural-products.png' WHERE id = 1360");
            $this->addSql("UPDATE menu_item SET logo = 'equipment-machinery.png' WHERE id = 1361");
            $this->addSql("UPDATE menu_item SET logo = 'packing.png' WHERE id = 1362");
            $this->addSql("UPDATE menu_item SET logo = 'services.png' WHERE id = 1363");
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
