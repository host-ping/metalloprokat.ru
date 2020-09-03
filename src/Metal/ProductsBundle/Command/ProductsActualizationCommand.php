<?php

namespace Metal\ProductsBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCounter;
use Metal\CompaniesBundle\Entity\PackageChecker;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProductsActualizationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:products:actualize-paid-clients');
        $this->addOption('twice-in-week', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        $scheduledActualizationTime = CompanyCounter::DEFAULT_SCHEDULED_ACTUALIZATION_TIME;
        if (!$input->getOption('twice-in-week')) {
            $scheduledActualizationTime = date('H');
        }

        $companiesQb = $em
            ->getRepository('MetalCompaniesBundle:Company')
            ->createQueryBuilder('c')
            ->join('c.counter', 'cc');

        if ($input->getOption('twice-in-week')) {
            $companiesQb->andWhere('(cc.productsActualizedAt < :date OR cc.productsActualizedAt IS NULL)')
                ->setParameter('date', new \DateTime('-2 week'));
        }

        $companiesQb
            ->andWhere('c.codeAccess IN (:codes)')
            ->andWhere('cc.scheduledActualizationTime = :scheduledActualizationTime')
            ->setParameter('codes', PackageChecker::getPackagesByOption('actualization_available'))
            ->setParameter('scheduledActualizationTime', $scheduledActualizationTime);

        $companies = $companiesQb->getQuery()->getResult();
        /* @var $companies Company[] */

        $productsActualizationService = $this->getContainer()->get('metal.products.product_actualization_service');

        foreach ($companies as $company) {
            $actualized = $productsActualizationService->actualizeProducts($company);

            if ($actualized) {
                $output->writeln(sprintf('%s: Actualized products for company "%d".', date('Y-m-d H:i:s'), $company->getId()));
            } else {
                $output->writeln(sprintf('%s: Products of company "%d" was actualized today.', date('Y-m-d H:i:s'), $company->getId()));
            }

        }

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
