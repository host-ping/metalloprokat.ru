<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150325123110 extends AbstractMigration implements ContainerAwareInterface
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
        if ($this->container->getParameter('project.family') == 'metalloprokat') {
            $this->addSql(
                "INSERT INTO grabber_site (title, slug, login, password, host, service, created_at, is_enabled) VALUES ('metallorus.ru', 'metallorus', 'ValeryF1@yandex.ru', 'aks999', 'http://metallorus.ru/', 'metallorus', '2015-01-28 13:21:02', 0)"
            );
        }
        
        $this->addSql("ALTER TABLE grabber_site DROP service");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
