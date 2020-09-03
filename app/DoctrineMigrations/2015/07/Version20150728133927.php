<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150728133927 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql(
                "
            DELETE FROM grabber_parsed_demand WHERE grabber_parsed_demand.id IN (
            SELECT demand.parsed_demand_id FROM demand
            WHERE demand.parsed_demand_id IS NOT NULL AND
                  (
                    demand.phone LIKE '%зарегистрированных пользователей%'
                    OR
                    demand.person LIKE '%зарегистрированных пользователей%'
                  )
            );
            "
            );

            $this->addSql(
                "
            DELETE FROM demand
            WHERE demand.parsed_demand_id IS NOT NULL AND
              (
                demand.phone LIKE '%зарегистрированных пользователей%'
                OR
                demand.person LIKE '%зарегистрированных пользователей%'
              );
            "
            );
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
