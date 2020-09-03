<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160701123238 extends AbstractMigration implements ContainerAwareInterface
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
        if ($this->container->getParameter('project.family') === 'product') {
                $this->addSql(' 
                    DELETE ppvalue FROM Message159 ppvalue WHERE ppvalue.Message_ID IN (
                    SELECT Message_ID FROM (
                        SELECT ppv.Message_ID FROM Message159 ppv
                        JOIN Message142 p ON p.Message_ID = ppv.Price_ID
                        WHERE ppv.GostM_ID = 2111 AND p.Category_ID = 81
                    ) tmp
                );
            ');
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
