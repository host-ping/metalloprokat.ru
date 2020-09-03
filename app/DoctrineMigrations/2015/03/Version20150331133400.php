<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150331133400 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql("
                  INSERT INTO grabber_site (title, host, login, password, created_at, is_enabled, code)
                  VALUES ('fruitinfo.ru', 'http://fruitinfo.ru', 'ValeryF1@yandex.ru', 'pivkos', '2015-03-30 13:38:10', 0, 'fruitinfo')
                  ");

            $this->addSql("
                  INSERT INTO grabber_site (title, host, login, password, created_at, is_enabled, code)
                  VALUES ('milknet.ru', 'http://milknet.ru', 'ValeryF1@yandex.ru', 'pivkos', '2015-03-30 13:38:10', 0, 'milknet')
                  ");

            $this->addSql("
                  INSERT INTO grabber_site (title, host, login, password, created_at, is_enabled, code)
                  VALUES ('sweetinfo.ru', 'http://sweetinfo.ru', 'ValeryF1@yandex.ru', 'pivkos', '2015-03-30 13:38:10', 0, 'sweetinfo')
                  ");

            $this->addSql("
                  INSERT INTO grabber_site (title, host, login, password, created_at, is_enabled, code)
                  VALUES ('drinkinfo.ru', 'http://drinkinfo.ru', 'ValeryF1@yandex.ru', 'pivkos', '2015-03-30 13:38:10', 0, 'drinkinfo')
                  ");

            $this->addSql("
                  INSERT INTO grabber_site (title, host, login, password, created_at, is_enabled, code)
                  VALUES ('vbakalee.ru', 'http://vbakalee.ru', 'ValeryF1@yandex.ru', 'pivkos', '2015-03-30 13:38:10', 0, 'vbakalee')
                  ");

            $this->addSql("
                  INSERT INTO grabber_site (title, host, login, password, created_at, is_enabled, code)
                  VALUES ('eqinfo.ru', 'http://eqinfo.ru', 'ValeryF1@yandex.ru', 'pivkos', '2015-03-30 13:38:10', 0, 'eqinfo')
                  ");

            $this->addSql("
                  INSERT INTO grabber_site (title, host, login, password, created_at, is_enabled, code)
                  VALUES ('packboard.ru', 'http://packboard.ru', 'ValeryF1@yandex.ru', 'pivkos', '2015-03-30 13:38:10', 0, 'packboard')
                  ");
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
