<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\ProjectBundle\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class SynchronizeCompanyCounterCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:companies:synchronize-counters');
        $this->addOption('company-id', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Array of companies', array());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        /* @var $em EntityManager */

        $em->getConfiguration()->setSQLLogger(null);
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $companyCounterRepository = $em->getRepository('MetalCompaniesBundle:CompanyCounter');

        $siteRepository =  $em->getRepository('MetalProjectBundle:Site');
        /* @var $siteRepository SiteRepository */

        $siteRepository->disableLogging();

        $companiesIds = $input->getOption('company-id');

        $output->writeln('Start synchronization companies');

        $companyCounterRepository->synchronizeCompanyCounters();
        $output->writeln('Start update reviews counter');
        $companyCounterRepository->updateReviewsCount($companiesIds);

        $output->writeln('Start update products counter');
        $companyCounterRepository->generalUpdateProductsCount($companiesIds);

        $output->writeln('Start update companies new complaints counter');
        $companyCounterRepository->updateCompaniesComplaintCount($companiesIds);

        $output->writeln('Start update companies new reviews counter');
        $companyCounterRepository->updateCompaniesNewReviewCount($companiesIds);

        $output->writeln('Start update companies new callback counter');
        $companyCounterRepository->updateCompaniesNewCallbacksCount($companiesIds);

        $output->writeln('Start update companies new demand counter');
        $companyCounterRepository->updateViewDemandCounter($companiesIds);

        $siteRepository->restoreLogging();

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }
}
