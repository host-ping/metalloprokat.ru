<?php

namespace Metal\StatisticBundle\Command;

use Doctrine\ORM\Tools\SchemaTool;
use Metal\AnnouncementsBundle\Entity\StatsElement as AnnouncementStatsElement;
use Metal\GrabbersBundle\Entity\GrabberLog;
use Metal\ProjectBundle\Entity\BanRequest;
use Metal\StatisticBundle\Entity\StatsElement;
use Metal\StatisticBundle\Entity\StatsProductChange;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateStatsTablesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:stats:create-stats-tables')
            ->addOption('connection', null, InputOption::VALUE_OPTIONAL, 'default|archive', 'default')
            ->addOption('engine', null, InputOption::VALUE_OPTIONAL, 'innodb|tokudb', 'innodb');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $connections = array(
            'default' => 'doctrine.dbal.default_connection',
            'archive' => 'doctrine.dbal.archive_connection',
        );

        $conn = $connections[$input->getOption('connection')];
        $conn = $this->getContainer()->get($conn);

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $metadataFactory = $em->getMetadataFactory();
        $tool = new SchemaTool($em);

        $classes = array(
            StatsElement::class,
            AnnouncementStatsElement::class,
            StatsProductChange::class,
            BanRequest::class,
            GrabberLog::class,
        );

        //TODO: add support of tokudb tables (use suffix "toku" for table names)
        foreach ($classes as $i => $class) {
            $classes[$i] = $metadataFactory->getMetadataFor($class);
        }

        foreach ($tool->getCreateSchemaSql($classes) as $sql) {
            try {
                $conn->executeUpdate($sql);
            } catch (\Exception $e) {
                $output->writeln(sprintf('%s: <error>%s</error>', date('d.m.Y H:i:s'), $e->getMessage()));
            }
        }

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
