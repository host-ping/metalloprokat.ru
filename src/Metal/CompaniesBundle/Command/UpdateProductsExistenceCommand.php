<?php

namespace Metal\CompaniesBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateProductsExistenceCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:update-products-existence');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $em = $this->getContainer()->get('doctrine');
        $em->getRepository('MetalCompaniesBundle:CompanyCity')->refreshBranchOfficeHasProducts();

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
