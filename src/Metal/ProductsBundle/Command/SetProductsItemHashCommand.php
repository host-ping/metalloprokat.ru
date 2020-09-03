<?php

namespace Metal\ProductsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Metal\ProductsBundle\Entity\Product;

class SetProductsItemHashCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:products:set-products-item-hash');
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $em = $this->getContainer()->get('doctrine');
        $conn = $em->getConnection();
        $conn->getConfiguration()->setSQLLogger(null);

        if ($input->getOption('truncate')) {
            $minId = $conn->fetchColumn('SELECT MIN(Message_ID) FROM Message142');
        } else {
            $minId = $conn->fetchColumn('SELECT MIN(Message_ID) FROM Message142 WHERE item_hash IS NULL');
        }

        $maxId = $conn->fetchColumn('SELECT MAX(Message_ID) FROM Message142');

        $idFrom = $minId;

        do {
            $idTo = $idFrom + 1000;
            $output->writeln(sprintf('%s: idFrom: %s idTo: %s', date('d.m.Y H:i:s'), $idFrom, $idTo));
            $products = $conn->fetchAll('
                SELECT p.Message_ID as id, p.branch_office_id, p.Name as title, p.Memo as size FROM Message142 AS p WHERE p.Message_ID >= :idFrom AND p.Message_ID < :idTo',
                array(
                        'idFrom' => $idFrom,
                        'idTo' => $idTo
                )
            );

            foreach ($products as $product) {
                $itemHash = Product::calculateItemHash($product['branch_office_id'], $product['title'], $product['size']);
                $conn->executeUpdate('UPDATE Message142 SET item_hash = :item_hash, LastUpdated=LastUpdated WHERE Message_ID = :id',
                    array(
                        'item_hash' => $itemHash,
                        'id' => $product['id']
                    )
                );
            }

            $idFrom = $idTo;
        } while ($idFrom <= $maxId);

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
