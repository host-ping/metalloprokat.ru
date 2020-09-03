<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150623142706 extends AbstractMigration implements ContainerAwareInterface
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
        if ($this->container->getParameter('project.family') === 'product') {
            $this->addSql("
                INSERT INTO redirect (redirect_from, redirect_to, enabled, created_at, updated_at) VALUES (
                  '#(.+)?/hleb-i-vypechka/smesi-dlya-vypechki/drozhi-120/(.+)?#ui',
                  '$1/hleb-i-vypechka/smesi-dlya-vypechki/drozhi/$2',
                  1,
                  '2015-06-23 12:39:20',
                  '2015-06-23 12:39:20'
                )"
            );
            $this->addSql("
                INSERT INTO redirect (redirect_from, redirect_to, enabled, created_at, updated_at) VALUES (
                  '#(.+)?/selhoztovary/semechki-395/(.+)?#ui',
                  '$1/selhoztovary/semechki/$2',
                  1,
                  '2015-06-23 12:39:20',
                  '2015-06-23 12:39:20'
                )"
            );
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
