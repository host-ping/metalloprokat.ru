<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160413152631 extends AbstractMigration implements ContainerAwareInterface
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
        if ($this->container->getParameter('project.family') === 'stroy') {
            $this->addSql("
                INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('ВЕРХНИЙ', 'head-center', 640, 100, 41500, 1, 1, 2);
                INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('ПРЕСТИЖ 1', 'head-side-1', 150, 100, 17500, 1, 1, 3);
                INSERT INTO announcement_zone (title, slug, width, height, cost, section_id, number, priority) VALUES ('ПРЕСТИЖ 2', 'head-side-2', 150, 100, 17500, 1, 1, 4);
            ");
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
