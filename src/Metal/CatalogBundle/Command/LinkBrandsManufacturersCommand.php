<?php

namespace Metal\CatalogBundle\Command;


use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LinkBrandsManufacturersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:catalog:link-brands-manufacturers');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        $em->getConfiguration()->setSQLLogger(null);
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $conn = $em->getConnection();

        $conn->executeUpdate('UPDATE catalog_brand SET manufacturer_id = NULL');

        $minId = $conn->fetchColumn('SELECT MIN(id) FROM catalog_product');
        $maxId = $conn->fetchColumn('SELECT MAX(id) FROM catalog_product');

        $idFrom = $minId;
        $brandsWithCollision = array();
        do {
            $idTo = $idFrom + 1000;
            $output->writeln(sprintf('%s: idFrom: %s idTo: %s', date('d.m.Y H:i:s'), $idFrom, $idTo));

            $products = $conn->fetchAll(
                'SELECT brand_id, manufacturer_id FROM catalog_product WHERE id >= :id_from AND id < :id_to GROUP BY manufacturer_id, brand_id',
                array(
                    'id_from' => $idFrom,
                    'id_to' => $idTo
                )
            );

            foreach ($products as $product) {
                $hasCollision = $conn->fetchColumn(
                    'SELECT id FROM catalog_brand WHERE id = :id AND manufacturer_id IS NOT NULL AND manufacturer_id <> :manufacturer_id',
                    array(
                        'id' => $product['brand_id'],
                        'manufacturer_id' => $product['manufacturer_id']
                    )
                );

                if ($hasCollision) {
                    $brandsWithCollision[$product['brand_id']] = true;
                }

                $conn->executeUpdate(
                    'UPDATE catalog_brand SET manufacturer_id = :manufacturer_id WHERE id = :id',
                    array(
                        'manufacturer_id' => $product['manufacturer_id'],
                        'id' => $product['brand_id']
                    )
                );
            }

            $idFrom = $idTo;
        } while ($idFrom <= $maxId);

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
        if ($brandsWithCollision) {
            $output->writeln('Collision brands IDS:');
            $output->writeln(print_r(array_keys($brandsWithCollision), true));
        }
    }
}
