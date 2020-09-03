<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\TerritorialBundle\Entity\Country;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160812174116 extends AbstractMigration implements ContainerAwareInterface
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
        $this->addSql('UPDATE Classificator_Region SET country_id = 92, display_in_country_id = :_country_id WHERE Region_ID = 2356',
            array('_country_id' => Country::COUNTRY_ID_RUSSIA)
        );
        $this->addSql('UPDATE Classificator_Regions SET country_id = 92 WHERE Regions_ID = 100');

        $this->container->get('doctrine')->getManager()->getRepository('MetalTerritorialBundle:TerritorialStructure')->populate();

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
