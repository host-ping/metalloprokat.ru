<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\CallbacksBundle\Entity\Callback;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160603115656 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $callbacksUsers = $this->connection->fetchAll("SELECT User_ID FROM User WHERE Email = 'v.fokin@metalloprokat.ru' OR Email = 'marina_f@product.ru' ");

        foreach ($callbacksUsers as $callbackUser) {
            $this->addSql('
                UPDATE callback 
                SET processed_at = NULL,
                    processed_by = NULL
                WHERE kind = :to_company
                AND processed_by = :user_id
                ', array('to_company' => Callback::CALLBACK_TO_SUPPLIER, 'user_id' => (int)$callbackUser['User_ID'])
            );
        }

        $em = $this->container->get('doctrine')->getManager();
        /** @var $em EntityManager */

        $em->getRepository('MetalCompaniesBundle:CompanyCounter')->updateCompaniesNewCallbacksCount(array());
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
