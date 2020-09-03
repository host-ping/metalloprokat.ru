<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\TerritorialBundle\Entity\Country;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150609172401 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        $hostnamesMap = $this->container->getParameter('hostnames_map');
        $hostInfo = reset($hostnamesMap);
        $domain = key($hostnamesMap);

        $this->addSql(
            '
                UPDATE Classificator_Country
                SET
                  domain_title = :domain_title,
                  base_host = :base_host,
                  currency_id = :currency_id
                WHERE Country_ID NOT IN (:countries_ids)',
            array(
                'base_host' => $domain,
                'domain_title' => $hostInfo['title'],
                'currency_id' => $hostInfo['currency_id'],
                'countries_ids' => Country::getEnabledCountriesIds()
            ),
            array('countries_ids' => Connection::PARAM_INT_ARRAY)
        );
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
