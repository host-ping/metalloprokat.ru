<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160325135423 extends AbstractMigration implements ContainerAwareInterface
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

            $this->addSql("UPDATE announcement_zone SET priority = 2 WHERE slug = 'head-center' ");
            $this->addSql("UPDATE announcement_zone SET priority = 3 WHERE slug = 'head-side-1' ");
            $this->addSql("UPDATE announcement_zone SET priority = 4 WHERE slug = 'head-side-2' ");
            $this->addSql("UPDATE announcement_zone SET priority = 5  WHERE slug = 'main-sub-1' ");
            $this->addSql("UPDATE announcement_zone SET priority = 6  WHERE slug = 'main-sub-2' ");
            $this->addSql("UPDATE announcement_zone SET priority = 7  WHERE slug = 'main-sub-3' ");
            $this->addSql("UPDATE announcement_zone SET priority = 8 WHERE slug = 'left-sidebar' ");
            $this->addSql("UPDATE announcement_zone SET priority = 9 WHERE slug = 'right-sidebar-1' ");
            $this->addSql("UPDATE announcement_zone SET priority = 10 WHERE slug = 'right-sidebar-2' ");
            $this->addSql("UPDATE announcement_zone SET priority = 11  WHERE slug = 'right-sidebar-3' ");
        }

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
