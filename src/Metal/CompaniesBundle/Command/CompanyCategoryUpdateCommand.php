<?php

namespace Metal\CompaniesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CompanyCategoryUpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:category-update');
        $this->addOption('company-id', null, InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, '', array());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $companiesIds = $input->getOption('company-id');

        if (!$companiesIds) {
            throw new \InvalidArgumentException('Option "company-id" value required.');
        }

        $em = $this->getContainer()->get('doctrine');
        $categoryDetector = $this->getContainer()->get('metal.categories.category_matcher');
        $em->getRepository('MetalCompaniesBundle:CompanyCategory')
            ->updateCompaniesCategories(array_fill_keys(array_values($companiesIds), array()), $categoryDetector::DEFAULT_CATEGORY_ID)
        ;

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
