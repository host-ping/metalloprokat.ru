<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151028115410 extends AbstractMigration implements ContainerAwareInterface
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
                INSERT INTO redirect
                (redirect_from, redirect_to, enabled, created_at, updated_at)
                VALUES
                ('#(.+)?/smes-ingridienti-dlya-vypechki/(.+)?#ui', '$1/smes-ingredienti-dlya-vypechki/$2', 1, '2015-10-28 11:51:08', '2015-10-28 11:51:22');
            ");
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
