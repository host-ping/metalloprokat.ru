<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150223161439 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema)
    {
        if ($this->container->getParameter('project.family') == 'product') {
            $this->addSql(
                'INSERT IGNORE INTO demand_subscription_category (user_id, category_id)
                              SELECT user.User_ID, companyCategory.cat_id FROM Message76 AS companyCategory
                                JOIN User AS user ON companyCategory.company_id = user.ConnectCompany'
            );
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
