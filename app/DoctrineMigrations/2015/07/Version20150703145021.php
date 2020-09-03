<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150703145021 extends AbstractMigration implements ContainerAwareInterface
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
                "INSERT INTO redirect (redirect_from, redirect_to, enabled, created_at, updated_at) VALUES
              ('#(.+)?/truba/trubabu/profilnaya(.+)?/(.+)?#ui', '$1/truba/trubast/profilnaya$2/$3', 1, '2015-07-03 14:45:04',
               '2015-07-03 14:45:09');"
            );
            $this->addSql(
                "INSERT INTO redirect (redirect_from, redirect_to, enabled, created_at, updated_at) VALUES
              ('#(.+)?/sort/balka/STO_ASCHM_20-93/(.+)?#ui', '$1/sort/balka/sto-aschm-20-93/$2', 1, '2015-07-03 14:48:44',
               '2015-07-03 14:48:48');"
            );
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
