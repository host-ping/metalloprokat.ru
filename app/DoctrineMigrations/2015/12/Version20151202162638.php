<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151202162638 extends AbstractMigration implements ContainerAwareInterface
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
                    "DELETE FROM UserSend WHERE subscribed_for_demands = TRUE
                     AND EXISTS(
                        SELECT * FROM metalspros_demand_subscription AS mds
                        WHERE
                          mds.id = demand_subscription_id
                        AND
                          mds.confirmed_at IS NULL
                    ) AND NOT EXISTS(
                        SELECT * FROM User AS u WHERE (u.User_ID = UserSend.user_id OR u.Email = UserSend.Email) AND u.Confirmed = TRUE
                    )"
            );
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
