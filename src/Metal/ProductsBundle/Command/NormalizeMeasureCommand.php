<?php

namespace Metal\ProductsBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NormalizeMeasureCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:products:normalize-measure');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        // этот апдейт нужно было выполнять только единожды. Во второй раз он перезатрет уже обработанные данные
        //$em->getConnection()->executeUpdate('UPDATE Message142 SET Pts = 1 WHERE Pts = 0');
        $em->getConnection()->executeUpdate('UPDATE Message142 SET Pts = 1 WHERE Pts = 58 OR Pts = 59');
        $em->getConnection()->executeUpdate('UPDATE Message142 SET Pts = 0 WHERE Pts = 2 OR Pts = 33');

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
