<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160226135643 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql("
                INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('ВЕРХНИЙ', 'head-center', 640, 100, 83000, 1, 1, 1);
                INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('ПРЕСТИЖ 1', 'head-side-1', 150, 100, 35000, 1, 1, 1);
                INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('ПРЕСТИЖ 2', 'head-side-2', 150, 100, 35000, 1, 1, 1);
                INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('VIP 1', 'main-sub-1', 200, 100, 35000, 1, 1, 1);
                INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('VIP 2', 'main-sub-2', 200, 100, 35000, 1, 1, 1);
                INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('VIP 3', 'main-sub-3', 200, 100, 35000, 1, 1, 1);
            ");

            $this->addSql("UPDATE announcement_zone SET cost = 75000 WHERE slug = 'left-sidebar' ");
            $this->addSql("UPDATE announcement_zone SET cost = 50000 WHERE slug = 'right-sidebar-1' ");
            $this->addSql("UPDATE announcement_zone SET cost = 35000 WHERE slug = 'right-sidebar-2' ");
            $this->addSql("UPDATE announcement_zone SET cost = 30000  WHERE slug = 'right-sidebar-3' ");
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
