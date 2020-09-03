<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151104163115 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql('CREATE TABLE ban_request_tmp LIKE ban_request');
            $this->addSql('INSERT INTO ban_request_tmp SELECT br.* FROM ban_request br JOIN ban_ip bi ON bi.int_ip = br.int_ip');
            $this->addSql('TRUNCATE ban_request');
            $this->addSql('INSERT INTO ban_request SELECT br.* FROM ban_request_tmp br');

            $this->addSql('DROP TABLE ban_request_tmp');
        }
        $this->addSql('ALTER TABLE ban_request ADD method VARCHAR(5) NOT NULL, ADD code SMALLINT DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
    }
}
