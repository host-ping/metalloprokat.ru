<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161028155102  extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql(
                'UPDATE instagram_account SET  parse_photos_tag_at = :prevDay,
               parse_photos_geo_tag_at = :prevDay,
               parse_photos_likers_at = :prevDay,
               like_all_at = :prevDay,
               like_likers_at = :prevDay,
               comments_at = :prevDay,
               refresh_stats_at = :prevDay,
               refresh_likers_at = :prevDay,
               get_likers_at = :prevDay,
               un_following_at = :prevDay,
               actualize_tag_at = :prevDay',
                array(
                    'prevDay' => new \DateTime('-2 day')
                ),
                array(
                    'prevDay' => 'date'
                )
            );
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
