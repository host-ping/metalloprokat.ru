<?php

namespace Metal\StatisticBundle\Command;

use Doctrine\DBAL\Connection;

use Metal\ProjectBundle\Doctrine\Utils;
use Metal\ProjectBundle\Util\InsertUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ImportProductChangesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:stats:import-product-changes')
            ->addOption(
                'connection',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'default|archive',
                array('default')
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $doctrine = $this->getContainer()->get('doctrine');
        $conn = $doctrine->getManager()->getConnection();
        /* @var $conn Connection */

        $defaultConnection = $this->getContainer()->get('doctrine.dbal.default_connection');

        $connections = array();
        if (in_array('default', $input->getOption('connection'))) {
            $connections[] = $this->getContainer()->get('doctrine.dbal.default_connection');
        }

        if (in_array('archive', $input->getOption('connection'))) {
            $connections[] = $this->getContainer()->get('doctrine.dbal.archive_connection');
        }
        /* @var $connections Connection[] */

        Utils::disableConnLogger($defaultConnection);
        foreach ($connections as $connection) {
            Utils::disableConnLogger($connection);
        }

        $range = $defaultConnection->fetchAssoc(
            'SELECT MIN(product.Message_ID) AS _min, MAX(product.Message_ID) AS _max FROM Message142 AS product
                    WHERE product.Company_ID IS NOT NULL AND product.is_virtual = FALSE'
        );

        $minId = $range['_min'];
        $maxId = $range['_max'];
        $idFrom = $minId;
        $limit = 1000;

        do {
            $idTo = $idFrom + $limit;

            $products = $defaultConnection->fetchAll(
                'SELECT product.Created AS date_created_at, product.Message_ID AS product_id, product.Company_ID AS company_id, 1 AS is_added FROM Message142 AS product
                    WHERE product.Company_ID IS NOT NULL AND product.is_virtual = FALSE
                     AND product.Message_ID >= :id_from AND product.Message_ID <= :id_to',
                array(
                    'id_from' => $idFrom,
                    'id_to' => $idTo
                ),
                array(
                    'id_from' => \PDO::PARAM_INT,
                    'id_to' => \PDO::PARAM_INT
                )
            );

            $output->writeln(sprintf('%s: Process idFrom "%s"', date('d.m.Y H:i:s'), $idFrom));

            foreach ($connections as $connection) {
                InsertUtil::insertMultipleOrUpdate($connection, 'stats_product_change', $products, array('is_added'), $limit);
            }

            $idFrom = $idTo;
        } while ($idFrom <= $maxId);

        $conn->executeQuery(
            'INSERT IGNORE INTO stats_product_change (date_created_at, product_id, company_id, is_added)
                    SELECT product.LastUpdated, product.Message_ID, product.Company_ID, 0 FROM Message142 AS product
                    WHERE product.Company_ID IS NOT NULL AND product.is_virtual = false
            '
        );

        Utils::restoreConnLogging($defaultConnection);
        foreach ($connections as $connection) {
            Utils::restoreConnLogging($connection);
        }

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
