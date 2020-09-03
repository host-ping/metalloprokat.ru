<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161108174353 extends AbstractMigration implements ContainerAwareInterface
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
            $this->addSql("UPDATE instagram_account_tag_comment AS tagComment
              JOIN instagram_account_tag AS iag ON iag.id = tagComment.instagram_account_tag_id
            SET tagComment.instagram_account_id = iag.instagram_account_id
            WHERE tagComment.instagram_account_tag_id IS NOT NULL");

            $this->addSql("UPDATE instagram_account_tag_comment AS tagComment
              JOIN instagram_account_location AS ial ON ial.id = tagComment.instagram_account_location_id
            SET tagComment.instagram_account_id = ial.instagram_account_id
            WHERE tagComment.instagram_account_location_id IS NOT NULL");
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
