<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160112181800 extends AbstractMigration implements ContainerAwareInterface
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
                INSERT INTO redirect
                (redirect_from, redirect_to, enabled, created_at, updated_at) VALUES
                  ('#(.+)?/selhoztovary/yaica/(.+)?#ui', '$1/molochnye-produkty/yaica-i-produkty/yaica/$2', 1, NOW(), NOW()),
                  ('#(.+)?/selhoztovary/sol/(.+)?#ui', '$1/sol_soda/sol/$2', 1, NOW(), NOW()),
                  ('#(.+)?/selhoztovary/sahar/(.+)?#ui', '$1/sahar-shokolad/sahar/$2', 1, NOW(), NOW()),
                  ('#(.+)?/selhoztovary/ovoshi/(.+)?#ui', '$1/frukty-ovoshhi/ovoshi/$2', 1, NOW(), NOW()),
                  ('#(.+)?/selhoztovary/frukty/(.+)?#ui', '$1/frukty-ovoshhi/frukty/$2', 1, NOW(), NOW()),
                  ('#(.+)?/selhoztovary/krupy/(.+)?#ui', '$1/krupy-bobovye/krupy/$2', 1, NOW(), NOW()),
                  ('#(.+)?/selhoztovary/bobovie/(.+)?#ui', '$1/krupy-bobovye/bobovie/$2', 1, NOW(), NOW())
            ");
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
