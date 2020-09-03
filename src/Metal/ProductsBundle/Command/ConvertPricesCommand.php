<?php

namespace Metal\ProductsBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\ProductsBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertPricesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:products:convert-prices');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        $conn = $em->getConnection();

        $conn->executeUpdate('UPDATE Message142 SET is_contract_price = 0');

        $conn->executeUpdate(
            'UPDATE Message142 SET is_contract_price = 1 WHERE Price RLIKE "^9999+$" OR Price = :val_contract OR Price = 0',
            array('val_contract' => Product::PRICE_CONTRACT)
        );

        $conn->executeUpdate('UPDATE Message142 SET is_price_from = 0');

        $conn->executeUpdate('UPDATE Message142 SET is_price_from = 1 WHERE Price_min > 0');

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
