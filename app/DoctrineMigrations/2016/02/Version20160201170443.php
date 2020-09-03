<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160201170443 extends AbstractMigration implements ContainerAwareInterface
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
        $this->addSql("ALTER TABLE Message75 CHANGE company_url company_url VARCHAR(1000) DEFAULT NULL COMMENT '(DC2Type:json_array)'");

        if ($this->container->getParameter('project.family') === 'metalloprokat') {
            $this->addSql("
              UPDATE Message75
                SET
                  company_url = '[\"http:\/\/www.zetek.ru\",\"http:\/\/www.timotion.ru\/\",\"http:\/\/www.stankoprivod.ru\/\",\"http:\/\/www.hiwin.com.ru\/\",\"http:\/\/www.zetek.spb.ru\",\"http:\/\/www.zetek.nsk.ru\",\"http:\/\/www.zetek-ural.ru\",\"http:\/\/www.prom-mehanika.ru\"]'
                WHERE Message_ID = 2035163;
            ");
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
