<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Metal\ContentBundle\Entity\ValueObject\StatusTypeProvider;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160702141437 extends AbstractMigration implements ContainerAwareInterface
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
                'UPDATE content_entry SET status_type_id = :checked WHERE status_type_id = :potential_spam',
                array(
                    'checked' => StatusTypeProvider::CHECKED,
                    'potential_spam' => StatusTypeProvider::POTENTIAL_SPAM,
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
