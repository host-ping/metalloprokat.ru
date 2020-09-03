<?php

namespace Metal\ProductsBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyPackage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ActualizationCompaniesUsingPromocodeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:products:actualization-companies-using-promocode');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));

        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */

        $companyPackageRepository = $em->getRepository('MetalCompaniesBundle:CompanyPackage');
        $companyRepository = $em->getRepository('MetalCompaniesBundle:Company');
        
        $companies = $companyRepository
            ->createQueryBuilder('company')
            ->select('company')
            ->addSelect('promocode')
            ->join('company.promocode', 'promocode')
            ->andWhere('company.deletedAtTS = 0')
            ->getQuery()
            ->getResult()
        ;
        /* @var $companies Company[] */

        $packages = $companyPackageRepository->findBy(array('company' => $companies));
        $packageToCompaniesIds = array();
        foreach ($packages as $package) {
            $packageToCompaniesIds[$package->getCompany()->getId()] = $package;
        }

        $companiesToUpdateProducts = array();
        foreach ($companies as $key => $company) {
            $package = isset($packageToCompaniesIds[$company->getId()]) ? $packageToCompaniesIds[$company->getId()] : null;
            if ($package instanceof CompanyPackage && !$package->isActive()) {
                
                $output->writeln(sprintf('%s: Company %d reset promocode."', date('Y-m-d H:i'), $company->getId()));
                
                $company->setPromocode(null);
                $em->flush($company);
                $companiesToUpdateProducts[$company->getId()] = true;
            }
        }

        if ($companiesToUpdateProducts) {
            $output->writeln(sprintf('%s: Update companies allowed products count."', date('Y-m-d H:i')));
            $this->getApplication()->find('metal:products:update-allowed-add-count-products')->run(
                new ArrayInput(
                    array(
                        'command' => 'metal:products:update-allowed-add-count-products',
                        '--company-id' => array_keys($companiesToUpdateProducts),
                    )
                ),
                $output
            );
        }

        $output->writeln(sprintf('%s: Success command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}