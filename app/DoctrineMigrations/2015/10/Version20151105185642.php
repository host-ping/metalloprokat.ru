<?php

namespace Application\Migrations;

use Behat\Transliterator\Transliterator;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\CategoriesBundle\Entity\ParameterOption;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151105185642 extends AbstractMigration implements ContainerAwareInterface
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
            $conn = $this->connection;
            $attributesValues = $conn->createQueryBuilder()
                ->select('av.value, av.id, a.code')
                ->from('attribute_value', 'av')
                ->join('av', 'attribute', 'a', 'av.attribute_id = a.id')
                ->orderBy('av.id')
                ->execute()
                ->fetchAll();

            foreach ($attributesValues as $attributeValue) {
                $slug = Transliterator::transliterate($attributeValue['value']);
                $slug = Transliterator::urlize($slug);
                $isDoubleSlug = (bool) $conn->fetchColumn(
                    'SELECT id FROM attribute_value WHERE old_slug = :double_slug',
                    array('double_slug' => $slug)
                );

                if ($isDoubleSlug) {
                    $slug = $attributeValue['code'].'-'.$slug;
                    $slug = ParameterOption::normalizeTitle($slug);
                }

                $conn->executeUpdate(
                    'UPDATE attribute_value SET old_slug = :slug WHERE id = :id',
                    array('slug' => $slug, 'id' => $attributeValue['id'])
                );
            }
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
