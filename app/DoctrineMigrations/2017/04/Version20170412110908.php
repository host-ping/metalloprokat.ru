<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170412110908 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql(
            'DELETE FROM user_visiting WHERE date < :date',
            array(
                'date' => \DateTime::createFromFormat('Y-m-d', $this->container->getParameter('project.copyright_year').'-01-01')
            ),
            array(
                'date' => 'date'
            )
        );

        $this->addSql(
            'UPDATE user_visiting AS uv 
              JOIN User AS u ON uv.user_id = u.User_ID 
              SET uv.company_id = u.ConnectCompany
              WHERE u.ConnectCompany IS NOT NULL
              '
        );
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
