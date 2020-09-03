<?php

namespace Application\Migrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150227162944 extends AbstractMigration implements ContainerAwareInterface
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
        $conn = $this->connection;
        /* @var $conn Connection */

        if ($this->container->getParameter('project.family') == 'product') {
            $delimiter = '\r\n|\r|\n';
            $categoriesPattern = $conn->executeQuery(
                "SELECT ce.*, category.allow_products FROM Message73 AS category
                JOIN category_extended ce ON category.Message_ID = ce.category_id
                WHERE ce.extended_pattern = :extended_pattern AND category.allow_products = :allow_products;",
                array(
                    'allow_products' => true,
                    'extended_pattern' => ''
                )
            )->fetchAll();

            foreach ($categoriesPattern as $categoryPattern) {
                if (!$categoryPattern['pattern']) {
                    continue;
                }

                $subjects = preg_split('/' . $delimiter . '/', $categoryPattern['pattern']);
                $extendedPattern = '';
                $multiple = false;
                if (count($subjects) > 1) {
                    $multiple = true;
                    $extendedPattern .= '(';
                }
                foreach ($subjects as $key => $subject) {
                    if ($multiple && $extendedPattern && $key > 0) {
                        $extendedPattern .= ' and ';
                    }

                    $extendedPattern .= sprintf('match(title, \'%s\')', $subject);
                }

                if ($multiple) {
                    $extendedPattern .= ')';
                }

                $conn->executeUpdate(
                    'UPDATE category_extended SET extended_pattern = :pattern WHERE category_id = :category_id',
                    array(
                        'pattern' => $extendedPattern,
                        'category_id' => $categoryPattern['category_id']
                    )
                );
            }
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
