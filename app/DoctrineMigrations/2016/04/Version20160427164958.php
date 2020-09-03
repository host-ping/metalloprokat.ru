<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\InstagramBundle\Entity\InstagramAccount;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160427164958 extends AbstractMigration implements ContainerAwareInterface
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
        if ($this->container->getParameter('project.family') === 'stroy') {
            $em = $this->container->get('doctrine.orm.default_entity_manager');
            $userName = $this->container->getParameter('instagram_uploader.username');
            $password = $this->container->getParameter('instagram_uploader.password');

            $user = new InstagramAccount();
            $user->setPassword($password);
            $user->setUserName($userName);
            $em->persist($user);
            $em->flush();
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}