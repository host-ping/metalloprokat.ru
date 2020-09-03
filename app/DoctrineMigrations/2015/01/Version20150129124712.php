<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150129124712 extends AbstractMigration implements ContainerAwareInterface
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
        $this->addSql("ALTER TABLE grabber_sites RENAME grabber_site");
        $this->addSql("ALTER TABLE grabber_site ADD slug VARCHAR(255) NOT NULL;");

        if ($this->container->getParameter('project.family') == 'metalloprokat') {
           $this->addSql("UPDATE grabber_site AS gs SET gs.slug = 'metal100' WHERE gs.id = 1");
           $this->addSql("UPDATE grabber_site AS gs SET gs.slug = 'tradeinox' WHERE gs.id = 2");
           $this->addSql("UPDATE grabber_site AS gs SET gs.slug = 'armtorg' WHERE gs.id = 3");
           $this->addSql("UPDATE grabber_site AS gs SET gs.slug = 'metaprom' WHERE gs.id = 4");
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
