<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150703172210 extends AbstractMigration implements ContainerAwareInterface
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
        $this->addSql("UPDATE grabber_site AS site SET site.use_proxy = false");

        if ($this->container->getParameter('project.family') === 'metalloprokat') {
            $this->addSql(
                "UPDATE grabber_site AS site SET site.use_proxy = true, site.test_proxy_uri = :test_proxy_uri WHERE site.code = :code",
                array(
                    'test_proxy_uri' => '/board',
                    'code' => 'armaturka'
                )
            );
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
