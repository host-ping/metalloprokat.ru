<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150330165557 extends AbstractMigration implements ContainerAwareInterface
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
        if ($this->container->getParameter('project.family') === 'product') {
            $this->addSql("INSERT INTO grabber_site (title, host, login, password, created_at, is_enabled, code) VALUES ('meatinfo.ru', 'http://meatinfo.ru', 'ValeryF1@yandex.ru', 'pivkos', '2015-03-30 16:58:10', 0, 'meatinfo')");
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
