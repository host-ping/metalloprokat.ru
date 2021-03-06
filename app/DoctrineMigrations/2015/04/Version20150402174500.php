<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150402174500 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql("INSERT INTO grabber_site (title, host, login, password, created_at, is_enabled, code) VALUES ('foodmarkets.ru', 'http://foodmarkets.ru', 'natasha5555577', 'bbpsLzBl', '2015-04-02 13:38:10', 0, 'foodmarkets')");
        }

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
