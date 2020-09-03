<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160427165641  extends AbstractMigration implements ContainerAwareInterface
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
        if ($this->container->getParameter('project.family') === 'stroy') {
            $this->addSql("
                   INSERT INTO instagram_liker
            SELECT id, 1, user_id, user_name, 1, user_full_name, created_at, updated_at FROM instagram_likers;
       ");

            $this->addSql("UPDATE instagram_stats SET instagram_account_id = 1");
            $this->addSql("UPDATE instagram_parse_tag SET instagram_account_id = 1, is_automatically_added = 1");
            $this->addSql("UPDATE instagram_stats SET liked_at = created_at WHERE liked_at IS NULL");
        }

        $this->addSql("DROP TABLE instagram_likers");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
