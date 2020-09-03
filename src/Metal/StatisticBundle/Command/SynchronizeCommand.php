<?php

namespace Metal\StatisticBundle\Command;

use Doctrine\DBAL\Connection;
use Metal\ProjectBundle\Doctrine\Utils;
use Metal\StatisticBundle\DataFetching\UpdateStatsSpec;
use Metal\StatisticBundle\Entity\StatsCategory;
use Metal\StatisticBundle\Entity\StatsCity;
use Metal\StatisticBundle\Entity\StatsDaily;
use Metal\StatisticBundle\Repository\ClientStatsRepository;
use Metal\StatisticBundle\Repository\StatsAnnouncementRepository;
use Metal\StatisticBundle\Repository\StatsDailyRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

class SynchronizeCommand extends ContainerAwareCommand
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Connection[]
     */
    private $readConnections = array();

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var \DateTime|null
     */
    private $dateStartFrom;

    /**
     * @var array
     */
    private $companiesIds = array();

    /**
     * @var ClientStatsRepository[]
     */
    private $statsRepositories = array();

    private static $availableRecalculateStats = array('counters', 'users_visiting', 'stats_element', 'stats_announcement', 'products_change');

    protected function configure()
    {
        $this
            ->setName('metal:stats:synchronize')
            ->addOption('truncate', null, InputOption::VALUE_NONE)
            ->addOption(
                'company-id',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY
            )
            ->addOption(
                'connection',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'default|archive',
                array('default')
            )
            ->addOption(
                'days',
                null,
                InputOption::VALUE_OPTIONAL,
                'Количество дней, которые нужно обработать (0 - сегодня, если не передавать - за доступные даты в рамках базы)'
            )
            ->addOption(
                'recalculate',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Пересчитать только определенные статистики: '.implode(', ', static::$availableRecalculateStats),
                static::$availableRecalculateStats
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $lock = new LockHandler($this->getName());
        if (!$lock->lock()) {
            $output->writeln(sprintf('The command "%s" is already running in another process.', $this->getName()));

            return 0;
        }

        $recalculate = array_unique((array) $input->getOption('recalculate'));
        $diff = array_diff($recalculate, static::$availableRecalculateStats);

        if ($diff) {
            throw new \InvalidArgumentException(sprintf('Value not support "%s"', implode(', ', $diff)));
        }

        $this->output = $output;
        $this->connection = $this->getContainer()->get('doctrine.dbal.default_connection');
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        Utils::checkEmConnection($em);

        $this->companiesIds = $input->getOption('company-id');
        $companyCounterRepo = $em->getRepository('MetalCompaniesBundle:CompanyCounter');

        $this->statsRepositories = array(
            StatsDaily::class => $em->getRepository('MetalStatisticBundle:StatsDaily'),
            StatsCity::class => $em->getRepository('MetalStatisticBundle:StatsCity'),
            StatsCategory::class => $em->getRepository('MetalStatisticBundle:StatsCategory'),
        );

        $allowedConnections = array(
            'archive' => 'doctrine.dbal.archive_connection',
            'default' => 'doctrine.dbal.default_connection',
        );

        foreach ((array) $input->getOption('connection') as $connectionName) {
            $output->writeln(sprintf('%s: Add <info>"%s"</info> connection.', date('d.m.Y H:i:s'), $connectionName));
            $connection = $this->getContainer()->get($allowedConnections[$connectionName]);
            Utils::checkConnection($connection);
            /* @var $connection Connection */
            $this->readConnections[] = $connection;
            Utils::disableConnLogger($connection);
        }

        Utils::disableLogger($em);
        $forDays = $input->getOption('days');
        if (null !== $forDays) {
            $this->dateStartFrom = (new \DateTime(sprintf('-%d days', $forDays)))->modify('midnight');
        }

        if ($this->dateStartFrom instanceof \DateTime) {
            if ($input->getOption('truncate')) {
                throw new \RuntimeException('You can not use both options truncate and days.');
            }
        } else {
            //NB! Не лочим таблицу если выполняем с опцией days
            $companyCounterRepo->changeLockStatus(true, $this->companiesIds);
        }

        if ($input->getOption('truncate')) {
            if (array_diff(static::$availableRecalculateStats, $recalculate)) {
                throw new \RuntimeException('You can not use both options truncate and recalculate.');
            }

            $output->writeln(sprintf('%s: Truncate <info>"stats_announcement"</info>', date('d.m.Y H:i:s')));
            $em->getRepository('MetalStatisticBundle:StatsAnnouncementDaily')->truncate();
            foreach ($this->statsRepositories as $statsRepository) {
                $this->checkConnection();
                $output->writeln(sprintf('%s: Truncate <info>"%s"</info>', date('d.m.Y H:i:s'), $statsRepository->getClassName()));
                $statsRepository->truncate();
            }
        }

        foreach ($recalculate as $action) {
            switch ($action) {
                case 'counters':
                    $this->updateCounters();
                    break;

                case 'users_visiting':
                    $this->updateUsersVisiting();
                    break;

                case 'stats_element':
                    $this->updateFromStatsElement();
                    break;

                case 'products_change':
                    $this->updateProductsChange();
                    break;

                case 'stats_announcement':
                    $this->updateStatsAnnouncement();
                    break;
            }
        }

        $this->checkConnection();
        $companyCounterRepo->changeLockStatus(false, $this->companiesIds);

        $lock->release();

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }

    private function updateUsersVisiting()
    {
        $statsDailyRepo = $this->statsRepositories[StatsDaily::class];
        /* @var $statsDailyRepo StatsDailyRepository */

        $this->output->writeln(sprintf('%s: Update <info>"%s"</info> users visiting statistic.', date('d.m.Y H:i:s'), $statsDailyRepo->getClassName()));

        $updateStatsSpec = $this->getUpdateStatsSpec();

        $statsDailyRepo->updateUsersOnSiteCounter($updateStatsSpec);

        $this->output->writeln(sprintf('%s: Done update <info>"%s"</info> users visiting statistic.', date('d.m.Y H:i:s'), $statsDailyRepo->getClassName()));
    }

    private function updateCounters()
    {
        $this->output->writeln(sprintf('%s: Update Counters.', date('d.m.Y H:i:s')));

        $updateStatsSpec = $this->getUpdateStatsSpec();

        foreach ($this->statsRepositories as $statsRepository) {
            $this->output->writeln(sprintf('%s: Update <info>"%s"</info> complaints statistic.', date('d.m.Y H:i:s'), $statsRepository->getClassName()));
            $statsRepository->updateComplaintsCounter(clone $updateStatsSpec);

            $this->output->writeln(sprintf('%s: Update <info>"%s"</info> callbacks statistic. ', date('d.m.Y H:i:s'), $statsRepository->getClassName()));
            $statsRepository->updateCallbacksCounter(clone $updateStatsSpec);

            $this->output->writeln(sprintf('%s: Update <info>"%s"</info> reviews statistic. ', date('d.m.Y H:i:s'), $statsRepository->getClassName()));
            $statsRepository->updateReviewsCounter(clone $updateStatsSpec);

            $this->output->writeln(sprintf('%s: Update <info>"%s"</info> demands statistic. ', date('d.m.Y H:i:s'), $statsRepository->getClassName()));
            $statsRepository->updateDemandsCounter(clone $updateStatsSpec);

            $this->output->writeln(sprintf('%s: Update <info>"%s"</info> demands views statistic. ', date('d.m.Y H:i:s'), $statsRepository->getClassName()));
            $statsRepository->updateDemandsViewsCounter(clone $updateStatsSpec);

            $this->output->writeln(sprintf('%s: Update <info>"%s"</info> demands to favorite statistic. ', date('d.m.Y H:i:s'), $statsRepository->getClassName()));
            $statsRepository->updateDemandsToFavoriteCounter(clone $updateStatsSpec);

            $this->output->writeln(sprintf('%s: Update <info>"%s"</info> demands answers statistic. ', date('d.m.Y H:i:s'), $statsRepository->getClassName()));
            $statsRepository->updateDemandsAnswersCounter(clone $updateStatsSpec);
        }

        $this->output->writeln(sprintf('%s: Done update Counters.', date('d.m.Y H:i:s')));
    }

    private function updateStatsAnnouncement()
    {

        $this->output->writeln(sprintf('%s: <comment>Update stats announcement statistic</comment>', date('d.m.Y H:i:s')));

        $announcementRepo = $this->getContainer()
            ->get('doctrine.orm.default_entity_manager')
            ->getRepository('MetalStatisticBundle:StatsAnnouncementDaily');
        /* @var $announcementRepo StatsAnnouncementRepository */

        $updateStatsSpec = $this->getUpdateStatsSpec();

        foreach ($this->readConnections as $connection) {
            $this->checkConnection();

            $dateRange = $announcementRepo->getStatsAnnouncementDateRange($connection);
            if (array_filter($dateRange)) {

                $updateStatsSpec->setDateFinish(new \DateTime($dateRange['_max']));

                if (null === $updateStatsSpec->dateStart) {
                    $updateStatsSpec->setDateFrom(new \DateTime($dateRange['_min']));
                    $updateStatsSpec->setDateTo(new \DateTime($dateRange['_min']));
                }

                do {
                    $this->output->writeln(
                        sprintf(
                            '%s: Update <info>"%s"</info> announcements count statistic for %s - %s dates.',
                            date('d.m.Y H:i:s'),
                            $announcementRepo->getClassName(),
                            $updateStatsSpec->dateFrom->format('d.m.Y'),
                            $updateStatsSpec->dateTo->format('d.m.Y')
                        )
                    );

                    $n = $announcementRepo->updateStatsAnnouncementCount($updateStatsSpec, $connection);

                    $this->output->writeln(
                        sprintf(
                            '%s: Found count <info>"%d"</info> "%s". Announcements count statistic for %s - %s dates.',
                            date('d.m.Y H:i:s'),
                            $n,
                            $announcementRepo->getClassName(),
                            $updateStatsSpec->dateFrom->format('d.m.Y'),
                            $updateStatsSpec->dateTo->format('d.m.Y')
                        )
                    );
                } while ($updateStatsSpec->nexDay());
            }
        }

        $this->output->writeln(sprintf('%s: <comment>Done update stats announcement statistic</comment>', date('d.m.Y H:i:s')));
    }

    private function updateProductsChange()
    {
        $this->output->writeln(sprintf('%s: <comment>Update products change statistic</comment>', date('d.m.Y H:i:s')));

        $updateStatsSpec = $this->getUpdateStatsSpec();

        $statsDailyRepo = $this->statsRepositories[StatsDaily::class];
        /* @var $statsDailyRepo StatsDailyRepository */

        foreach ($this->readConnections as $connection) {
            $this->checkConnection();

            $dateRange = $statsDailyRepo->getProductsChangesDateRange($connection, $this->companiesIds);

            if (array_filter($dateRange)) {
                $updateStatsSpec->setDateFinish(new \DateTime($dateRange['_max']));

                if (null === $updateStatsSpec->dateStart) {
                    $updateStatsSpec->setDateFrom(new \DateTime($dateRange['_min']));
                    $updateStatsSpec->setDateTo(new \DateTime($dateRange['_min']));
                }

                do {
                    $this->output->writeln(
                        sprintf(
                            '%s: Update <info>"%s"</info> stats products change statistic for %s - %s dates.',
                            date('d.m.Y H:i:s'),
                            $statsDailyRepo->getClassName(),
                            $updateStatsSpec->dateFrom->format('d.m.Y'),
                            $updateStatsSpec->dateTo->format('d.m.Y')
                        )
                    );

                    Utils::checkConnection($connection);
                    Utils::checkConnection($this->connection);
                    $count = $statsDailyRepo->updateProductsChangesStats($updateStatsSpec, $connection);

                    $this->output->writeln(
                        sprintf(
                            '%s: Found count <info>"%d"</info> stats products change rows "%s". statistic for %s - %s dates.',
                            date('d.m.Y H:i:s'),
                            $count,
                            $statsDailyRepo->getClassName(),
                            $updateStatsSpec->dateFrom->format('d.m.Y'),
                            $updateStatsSpec->dateTo->format('d.m.Y')
                        )
                    );

                } while ($updateStatsSpec->nexDay());
            }
        }

        $this->output->writeln(sprintf('%s: <comment>Done update products change statistic</comment>', date('d.m.Y H:i:s')));
    }

    private function updateFromStatsElement()
    {
        $updateStatsSpec = $this->getUpdateStatsSpec();

        $this->output->writeln(sprintf('%s: <comment>Update phones, products, website statistic</comment>', date('d.m.Y H:i:s')));
        foreach ($this->statsRepositories as $statsRepository) {
            foreach ($this->readConnections as $connection) {
                $this->checkConnection();

                $dateRange = $statsRepository->getStatsElementDateRange($connection, $this->companiesIds);

                if (array_filter($dateRange)) {
                    $updateStatsSpec->setDateFinish(new \DateTime($dateRange['_max']));

                    if (null === $updateStatsSpec->dateStart) {
                        $updateStatsSpec->setDateFrom(new \DateTime($dateRange['_min']));
                        $updateStatsSpec->setDateTo(new \DateTime($dateRange['_min']));
                    }

                    do {
                        $this->output->writeln(
                            sprintf(
                                '%s: Update <info>"%s"</info> phones, products, website statistic for %s - %s dates.',
                                date('d.m.Y H:i:s'),
                                $statsRepository->getClassName(),
                                $updateStatsSpec->dateFrom->format('d.m.Y'),
                                $updateStatsSpec->dateTo->format('d.m.Y')
                            )
                        );

                        $n = $statsRepository->updateStatsElementStats($updateStatsSpec, $connection);

                        $this->output->writeln(
                            sprintf(
                                '%s: Found count <info>"%d"</info> stats elements rows "%s". phones, products, website count statistic for %s - %s dates.',
                                date('d.m.Y H:i:s'),
                                $n,
                                $statsRepository->getClassName(),
                                $updateStatsSpec->dateFrom->format('d.m.Y'),
                                $updateStatsSpec->dateTo->format('d.m.Y')
                            )
                        );

                    } while ($updateStatsSpec->nexDay());
                }
            }
        }

        $this->output->writeln(sprintf('%s: <comment>Done update phones, products, website statistic</comment>', date('d.m.Y H:i:s')));
    }

    private function checkConnection()
    {
        foreach ($this->readConnections as $connection) {
            Utils::checkConnection($connection);
        }

        Utils::checkConnection($this->connection);
    }

    protected function getUpdateStatsSpec()
    {
        return (new UpdateStatsSpec())
            ->setCompaniesIds($this->companiesIds)
            ->setDateStart($this->dateStartFrom);
    }
}
