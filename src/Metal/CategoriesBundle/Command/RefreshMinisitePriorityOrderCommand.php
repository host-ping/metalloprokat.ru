<?php

namespace Metal\CategoriesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshMinisitePriorityOrderCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this->setName('metal:categories:refresh-minisite-priority-order');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i:s')));

        $this->getContainer()->get('doctrine.orm.default_entity_manager')
            ->getRepository('MetalCategoriesBundle:ParameterOption')
            ->refreshPriorityOrders();

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i:s')));
    }
}
