<?php

namespace Metal\StatisticBundle\Command;

use Doctrine\DBAL\Connection;
use Metal\ProjectBundle\Doctrine\Utils;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveBotEntriesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:stats:remove-bot-entries')
            ->addOption(
                'connection',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'default|archive',
                array('default', 'archive')
            )
            ->addOption(
                'table',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'announcement_stats_element|stats_element',
                array('announcement_stats_element', 'stats_element')
            )
            ->addOption(
                'ip',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Список ip аддресов'
            )
            ->addOption(
                'user-agent',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Список user-agent'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        if (!$input->getOption('ip') && !$input->getOption('user-agent')) {
            $output->writeln(sprintf('%s: Required option "ip" or "user-agent".', date('d.m.Y H:i:s')));

            return 0;
        }

        $connections = array(
            'archive' => $this->getContainer()->get('doctrine.dbal.archive_connection'),
            'default' => $this->getContainer()->get('doctrine.dbal.default_connection'),
        );
        /* @var $connections Connection[] */

        $cleanupResults = [];
        foreach ((array) $input->getOption('connection') as $connection) {
            foreach ((array) $input->getOption('table') as $table) {
                $conn = $connections[$connection];

                Utils::checkConnection($conn);

                $qb = $conn
                    ->createQueryBuilder()
                    ->delete($table);

                $orX = $qb->expr()->orX();
                if (!empty($input->getOption('ip'))) {
                    $orX->add($qb->expr()->in('ip', ':ip'));
                    $qb->setParameter('ip', $input->getOption('ip'), Connection::PARAM_STR_ARRAY);
                }

                if (!empty($input->getOption('user-agent'))) {
                    $orX->add($qb->expr()->in('user_agent', ':user_agent'));
                    $qb->setParameter('user_agent', $input->getOption('user-agent'), Connection::PARAM_STR_ARRAY);
                }

                $count = $qb->andWhere($orX)->execute();
                $cleanupResults[$connection][$table] = $count;

                $output->writeln(
                    sprintf(
                        '%s: Removed %d bot entries from table "%s" and connection "%s"',
                        date('d.m.Y H:i:s'),
                        $count,
                        $table,
                        $connection
                    )
                );
            }
        }

        $command = $this->getApplication()->find('metal:stats:synchronize');

        foreach ($cleanupResults as $connection => $cleanupResult) {
            foreach ($cleanupResult as $table => $count) {
                if ($count <= 0) {
                    $output->writeln(sprintf('%s: Command does not affect, recalculate not run.', date('d.m.Y H:i:s')));

                    continue;
                }

                $arguments = [
                    'command' => 'metal:stats:synchronize',
                    '--connection' => [$connection],
                    '--recalculate' => [$table],
                ];

                $input = new ArrayInput($arguments);
                $command->run($input, $output);
            }
        }

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }
}
