<?php

namespace Metal\StatisticBundle\Command;

use Doctrine\DBAL\Connection;
use Metal\ProjectBundle\Doctrine\Utils;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveBotsFromAnnouncementStatsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:stats:remove-bots-from-announcement-stats')
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
            $rangeIds = $connection->fetchAssoc('SELECT MIN(id) AS minId, MAX(id) AS maxId FROM announcement_stats_element');

            $i = 0;
            $limit = 200;
            $idFrom = $rangeIds['minId'];

            $removedCount = 0;
            do {
                Utils::checkConnection($connection);
                $idTo = $idFrom + $limit;
                $announcements = $connection->fetchAll(
                    'SELECT ase.id, ase.user_agent, ase.ip FROM announcement_stats_element AS ase WHERE ase.id >= :id_from AND ase.id < :id_to ',
                    array(
                        'id_from' => $idFrom,
                        'id_to' => $idTo
                    )
                );

                $botsIds = array();
                foreach ($announcements as $announcement) {
                    $botMetadata = $detector->detect($announcement['user_agent'], $announcement['ip']);

                    if (null !== $botMetadata) {
                        $botsIds[] = $announcement['id'];
                        $removedCount++;
                    }
                }

                if ($botsIds) {
                    Utils::checkConnection($connection);
                    $connection->executeUpdate('DELETE FROM announcement_stats_element WHERE id IN (:ids)',
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
