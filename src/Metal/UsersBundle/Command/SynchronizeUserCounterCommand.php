<?php

namespace Metal\UsersBundle\Command;

use Metal\ProjectBundle\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SynchronizeUserCounterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:users:synchronize-counters');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $userCounterRepository = $container->get('doctrine')->getRepository('MetalUsersBundle:UserCounter');
        /* @var $userCounterRepository \Metal\UsersBundle\Repository\UserCounterRepository */

        $siteRepository = $container->get('doctrine')->getRepository('MetalProjectBundle:Site');
        /* @var $siteRepository SiteRepository */

        $siteRepository->disableLogging();

        $output->writeln('Start synchronization users');
        $userCounterRepository->synchronizeUsersCounters();

        $output->writeln('Start counters recalculation');
        $userCounterRepository->updateUsersCounters(array());

        $siteRepository->restoreLogging();
    }
}
