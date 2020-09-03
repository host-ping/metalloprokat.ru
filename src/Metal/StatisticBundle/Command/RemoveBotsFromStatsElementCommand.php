<?php

namespace Metal\StatisticBundle\Command;

use Doctrine\DBAL\Connection;
use Metal\ProjectBundle\Doctrine\Utils;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveBotsFromStatsElementCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:stats:remove-bots-from-stats-element')
            ->addOption(
                'connection',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'default|archive',
                array('default')
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $connections = array(
            'archive' => 'doctrine.dbal.archive_connection',
            'default' => 'doctrine.dbal.default_connection'
        );

        $detector = $this->getContainer()->get('vipx_bot_detect.detector');
        foreach ($input->getOption('connection') as $conn) {
            $connection = $this->getContainer()->get($connections[$conn]);
            /* @var $connection Connection */
            Utils::disableConnLogger($connection);
            Utils::checkConnection($connection);
            $rangeIds = $connection->fetchAssoc('SELECT MIN(id) AS minId, MAX(id) AS maxId FROM stats_element');

            $i = 0;
            $limit = 200;
            $idFrom = $rangeIds['minId'];

            $removedCount = 0;
            do {
                Utils::checkConnection($connection);
                $idTo = $idFrom + $limit;
                $statsElements = $connection->fetchAll(
                    'SELECT se.id, se.user_agent, se.ip FROM stats_element AS se WHERE se.id >= :id_from AND se.id < :id_to ',
                    array(
                        'id_from' => $idFrom,
                        'id_to' => $idTo
                    )
                );

                $botsIds = array();
                foreach ($statsElements as $statsElement) {
                    $botMetadata = $detector->detect($statsElement['user_agent'], $statsElement['ip']);

                    if (null !== $botMetadata) {
                        $botsIds[] = $statsElement['id'];
                        $removedCount++;
                    }
                }

                if ($botsIds) {
                    Utils::checkConnection($connection);
                    $connection->executeUpdate('DELETE FROM stats_element WHERE id IN (:ids)',
                        array(
                            'ids' => $botsIds
                        ),
                        array(
                            'ids' => Connection::PARAM_INT_ARRAY
                        )
                    );
                }

                $idFrom = $idTo;

                $i++;
                if ($i % 50 === 0) {
                    $output->writeln($idTo.' / '.$rangeIds['maxId'].' '.date('d.m.Y H:i:s'));
                }
            } while ($idFrom <= $rangeIds['maxId']);

            $output->writeln(sprintf('%s: Done command. Removed %s bot entries. "%s" Connection: "%s"', date('d.m.Y H:i:s'), $removedCount, $this->getName(), $connections[$conn]));
        }
    }
}
