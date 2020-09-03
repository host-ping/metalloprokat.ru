<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160406114012 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql('UPDATE Message180 SET Priority = 1 WHERE Message_ID = 1');
            $this->addSql('UPDATE Message180 SET Priority = 3 WHERE Message_ID = 2');
            $this->addSql('UPDATE Message180 SET Priority = 4 WHERE Message_ID = 3');
            $this->addSql('UPDATE Message180 SET Priority = 2 WHERE Message_ID = 4');
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
