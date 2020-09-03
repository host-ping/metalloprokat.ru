<?php

namespace Metal\CatalogBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\CatalogBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateBrandsAndManufacturersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:catalog:create-brands-and-manufacturers')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $em->getConfiguration()->setSQLLogger(null);
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $conn = $em->getConnection();


        $conn->executeQuery('INSERT INTO catalog_brand (id, attribute_value_id, title, slug)
            (
            SELECT av.id, av.id, av.value, av.slug FROM attribute_value AS av JOIN attribute AS a ON av.attribute_id = a.id AND a.code = :code
            ) ON DUPLICATE KEY UPDATE title = av.value, slug = av.slug
        ', array('code' => Product::ATTR_CODE_BRAND));

        $conn->executeQuery('INSERT INTO catalog_manufacturer (id, attribute_value_id, title, slug)
            (
            SELECT av.id, av.id, av.value, av.slug FROM attribute_value AS av JOIN attribute AS a ON av.attribute_id = a.id AND a.code = :code
            ) ON DUPLICATE KEY UPDATE title = av.value, slug = av.slug
        ', array('code' => Product::ATTR_CODE_MANUFACTURER));


        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}