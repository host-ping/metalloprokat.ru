<?php

namespace Metal\ProjectBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SphinxOptimizeIndexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:sphinx-optimize-index');
        $this->addOption('index', null, InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
            'Список индексов на оптимизацию.', array('products', 'demands'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $sconn = $this->getContainer()->get('sphinxy.default_connection');

        foreach ((array)$input->getOption('index') as $index) {
            $output->writeln(sprintf('%s: OPTIMIZE INDEX %s', date('d.m.Y H:i:s'), $index));
            $sconn->executeUpdate(sprintf('OPTIMIZE INDEX %s', $index));
        }

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
