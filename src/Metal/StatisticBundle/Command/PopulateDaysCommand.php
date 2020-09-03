<?php

namespace Metal\StatisticBundle\Command;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateDaysCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:stats:populate-days');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $doctrine = $this->getContainer()->get('doctrine');
        $conn = $doctrine->getManager()->getConnection();
        /* @var $conn Connection */

        $conn->getConfiguration()->setSQLLogger(null);

        $startDate = new \DateTime('2008-01-01');
        $endDate = new \DateTime('+5 years');

        do {
            $conn->executeQuery('INSERT IGNORE INTO stats_day SET `date` = :date', array('date' => $startDate->format('Y-m-d')));

            $startDate->modify('+1 day');
        } while ($startDate < $endDate);

        $conn->executeUpdate('UPDATE stats_day sd SET year__month = YEAR(sd.date) * 100 + MONTH(sd.date), year__week = YEARWEEK(sd.date, 1)');

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }
}
