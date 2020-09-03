<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160525174050 extends AbstractMigration implements ContainerAwareInterface
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
            $conn = $this->container->get('doctrine.orm.default_entity_manager')->getConnection();

            $tagsToDisable = array();
            $response = $conn->fetchAll('SELECT id, title FROM instagram_account_tag WHERE enabled = 1');
            foreach ($response as $el) {
                if (!preg_match('/^[А-Я0-9_]+$/ui', $el['title'])) {
                    $tagsToDisable[$el['id']] = $el['title'];
                }
            }

            foreach ($tagsToDisable as $key => $tag) {
                $this->addSql(
                    'UPDATE instagram_account_tag SET enabled = 0 WHERE id = :id',
                    array(
                        'id' => $key
                    )
                );
            }
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
