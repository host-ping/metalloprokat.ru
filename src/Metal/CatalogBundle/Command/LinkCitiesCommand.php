<?php

namespace Metal\CatalogBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LinkCitiesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:catalog:link-cities')
            ->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $em->getConfiguration()->setSQLLogger(null);
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $conn = $em->getConnection();
        $now = new \DateTime();

        if ($input->getOption('truncate')) {
            $output->writeln('Truncate brand_city and manufacturer_city');
            $conn->executeQuery('TRUNCATE brand_city');
            $conn->executeQuery('TRUNCATE manufacturer_city');
        }

        $conn->executeQuery(
            'INSERT IGNORE INTO brand_city (brand_id, city_id, created_at, updated_at)
            (
                SELECT cp.brand_id, cpc.city_id, :now, :now FROM catalog_product AS cp
                JOIN catalog_product_city AS cpc
                ON cp.id = cpc.product_id
                GROUP BY cp.brand_id, cpc.city_id
            )
        ',
            array('now' => $now),
            array('now' => 'datetime')
        );

        $conn->executeQuery(
            'INSERT IGNORE INTO manufacturer_city (manufacturer_id, city_id, created_at, updated_at)
            (
                SELECT cp.manufacturer_id, cpc.city_id, :now, :now FROM catalog_product AS cp
                JOIN catalog_product_city AS cpc
                ON cp.id = cpc.product_id
                GROUP BY cp.manufacturer_id, cpc.city_id
            )
        ',
            array('now' => $now),
            array('now' => 'datetime')
        );

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}