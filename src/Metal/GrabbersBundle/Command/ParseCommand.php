<?php

namespace Metal\GrabbersBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ParseCommand extends ContainerAwareCommand
{
    const COMMAND_NAME = 'metal:grabbers:parse';

    protected function configure()
    {
        $this->setName(self::COMMAND_NAME);
        $this->addOption('site-code', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY);
        $this->addOption('start-page', null, InputOption::VALUE_OPTIONAL, '', 0);
        $this->addOption('force', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $grabberManager = $this->getContainer()->get('metal.grabbers.graber_manager');
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $output->writeln(sprintf('%s: Delete logs older than 5 days.', date('d.m.Y H:i:s')));

        $em->createQueryBuilder()
            ->delete('MetalGrabbersBundle:GrabberLog', 'grabberLog')
            ->where('grabberLog.createdAt < :date')
            ->setParameter('date', new \DateTime('-5 day'))
            ->getQuery()
            ->getResult()
        ;

        $force = (bool)$input->getOption('force');
        if (!$input->getOption('site-code')) {
            $grabberManager->manager($force);
        } else {
            $grabberManager->grab($input->getOption('site-code'), $force);
        }

        $output->writeln(sprintf('%s: Finish command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
