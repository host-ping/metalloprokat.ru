<?php

namespace Metal\StatisticBundle\Command;

use Metal\ProjectBundle\Doctrine\Utils;
use Metal\ProjectBundle\Util\InsertUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RecalculateStatsProductChangePerMonthCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:recalculate-stats-product-change-per-month')
            ->setDescription('Подсчет происходит с первого до последнего дня месяца.')
            ->addOption('archive', null, InputOption::VALUE_NONE, 'Get data from archive database.')
            ->addOption('date-from', null, InputOption::VALUE_OPTIONAL, 'Format "Y-m"')
            ->addOption('date-to', null, InputOption::VALUE_OPTIONAL, 'Format "Y-m"');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $dateFrom = new \DateTime($input->getOption('date-from') ?: '');
        $dateTo = new \DateTime($input->getOption('date-to') ?: '');

        $dateFrom->modify('first day of this month');
        $dateTo->modify('last day of this month');

        if ($dateFrom > $dateTo) {
            throw new \InvalidArgumentException(sprintf(
                'Неправильно введены даты date-from = %s , date-to = %s.',
                $dateFrom->format('Y-m'),
                $dateTo->format('Y-m')
            ));
        }

        $dateRanges = array();
        $years = $dateFrom->diff($dateTo)->y;
        $months = $dateFrom->diff($dateTo)->m;
        for ($i = 0; $i <= $years; $i++) {
            for ($j = 0; $j <= $months; $j++) {
                $tmpDate = clone $dateFrom;
                $dateRanges[] = array(
                    'date_from' => clone $tmpDate
                        ->modify(sprintf('+%d years', $i))
                        ->modify(sprintf('+%d month', $j))
                        ->modify('first day of this month'),
                    'date_to' => clone $tmpDate->modify('last day of this month'),
                );
            }
        }

        $container = $this->getContainer();
        $connections = array(
            'archive' => $container->get('doctrine.dbal.archive_connection'),
            'default' => $container->get('doctrine.dbal.default_connection')
        );

        foreach ($dateRanges as $dateRange) {
            $statsProductChanges = array();
            foreach ($connections as $connectionName => $connection) {
                $output->writeln(
                    sprintf(
                        '%s: Proccess date %s - %s. for connection %s',
                        date('d.m.Y H:i:s'),
                        $dateRange['date_from']->format('Y-m'),
                        $dateRange['date_to']->format('Y-m'),
                        $connectionName
                    )
                );

                Utils::checkConnection($connection);
                $statsProductChanges[$connectionName] = $connection->fetchAll(
                    '
                SELECT DISTINCT product_id, DATE_FORMAT(date_created_at, "%Y-%m-01") AS date
                FROM stats_product_change WHERE date_created_at >= :date_from AND date_created_at <= :date_to
              ',
                    array(
                        'date_from' => $dateRange['date_from'],
                        'date_to' => $dateRange['date_to']
                    ),
                    array(
                        'date_from' => 'date',
                        'date_to' => 'date'
                    )
                );
            }

            $resultArray = array();
            foreach ($statsProductChanges as $connectionName => $data) {
                foreach ((array) $data as $item) {
                    $resultArray[$item['date']][$item['product_id']] = true;
                }
            }

            unset($statsProductChanges);

            $countResult = array();
            foreach ($resultArray as $date => $result) {
                $countResult[] = array('date' => $date, 'count' => count($result));
            }

            unset($resultArray);

            if (!$countResult) {
                $output->writeln(
                    sprintf(
                        '%s: Date  <info> %s - %s </info> is empty.',
                        date('d.m.Y H:i:s'),
                        $dateRange['date_from']->format('Y-m'),
                        $dateRange['date_to']->format('Y-m'))
                );
                continue;
            }

            $output->writeln(
                sprintf(
                    '%s: Found products <info>%d</info> changes for <info> %s - %s </info> dates.',
                    date('d.m.Y H:i:s'),
                    $countResult[0]['count'],
                    $dateRange['date_from']->format('Y-m'),
                    $dateRange['date_to']->format('Y-m')
                )
            );

            Utils::checkConnection($connections['default']);

            InsertUtil::insertMultipleOrUpdate(
                $connections['default'],
                'stats_product_change_per_month',
                $countResult,
                array('count'),
                1000
            );
        }

        $output->writeln(sprintf('%s: Finish command.', date('d.m.Y H:i:s')));
    }
}
