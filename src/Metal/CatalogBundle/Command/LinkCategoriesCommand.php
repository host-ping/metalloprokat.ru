<?php

namespace Metal\CatalogBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class LinkCategoriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:catalog:link-categories')
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
            $output->writeln('Truncate brand_category and manufacturer_category');
            $conn->executeQuery('TRUNCATE brand_category');
            $conn->executeQuery('TRUNCATE manufacturer_category');
        }

        $conn->executeQuery(
            'INSERT IGNORE INTO brand_category (brand_id, category_id, created_at, updated_at)
            (
                SELECT brand_id, category_id, :now, :now FROM catalog_product GROUP BY brand_id, category_id
            )
        ',
            array('now' => $now),
            array('now' => 'datetime')
        );

        $conn->executeQuery(
            'INSERT IGNORE INTO manufacturer_category (manufacturer_id, category_id, created_at, updated_at)
            (
                SELECT manufacturer_id, category_id, :now, :now FROM catalog_product GROUP BY manufacturer_id, category_id
            )
        ',
            array('now' => $now),
            array('now' => 'datetime')
        );

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
