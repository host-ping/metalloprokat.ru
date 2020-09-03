<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160805140829 extends AbstractMigration implements ContainerAwareInterface
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
        $this->addSql("ALTER TABLE instagram_user ADD is_enabled TINYINT(1) DEFAULT 0 NULL;");
        if ($this->container->getParameter('project.family') === 'stroy') {
            $this->addSql("INSERT INTO instagram_user(id, name, is_enabled) VALUES (2, 'stroy.ru', 1)");
            $this->addSql("INSERT INTO instagram_user_tag (user_id, title) VALUES (2, 'Архитектура')");
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
