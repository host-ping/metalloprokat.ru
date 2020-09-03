<?php

namespace Metal\SupportBundle\Command;

use Metal\ProjectBundle\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SynchronizeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:support:synchronize-counters')
        ;
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $registry = $this->getContainer()->get('doctrine');

        $siteRepository = $registry->getRepository('MetalProjectBundle:Site');
        /* @var $siteRepository SiteRepository */

        $topicRepo = $registry->getRepository("MetalSupportBundle:Topic");

        $siteRepository->disableLogging();

        $output->writeln(sprintf('%s: answers', date('d.m.Y H:i:s')));
        $topicRepo->updateAnswersCount(array());

        $siteRepository->restoreLogging();

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }
}
