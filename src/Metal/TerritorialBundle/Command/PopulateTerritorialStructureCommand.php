<?php

namespace Metal\TerritorialBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateTerritorialStructureCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:territorial:populate-structure');
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $conn = $em->getConnection();

        if ($input->getOption('truncate')) {
            $output->writeln(sprintf('Truncate territorial_structure'));
            $conn->executeQuery('TRUNCATE territorial_structure');
        }

        $em->getRepository('MetalTerritorialBundle:TerritorialStructure')->populate(
            function ($line) use ($output) {
                $output->writeln($line);
            }
        );

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
