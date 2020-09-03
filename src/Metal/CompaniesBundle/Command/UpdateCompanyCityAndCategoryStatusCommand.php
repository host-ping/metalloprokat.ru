<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCity;
use Metal\CompaniesBundle\Entity\PackageChecker;
use Metal\CompaniesBundle\Repository\CompanyCityRepository;
use Metal\ProjectBundle\Doctrine\Utils;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateCompanyCityAndCategoryStatusCommand extends ContainerAwareCommand
{
    /**
     * @var CompanyCityRepository
     */
    private $companyCityRepository;

    /**
     * @var Connection
     */
    private $connection;


    protected function configure()
    {
        $this->setName('metal:companies:update-company-city-and-category-status');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $this->connection = $em->getConnection();

        Utils::disableLogger($em);

        $this->companyCityRepository = $em->getRepository('MetalCompaniesBundle:CompanyCity');
        $companyCategoryRepository = $em->getRepository('MetalCompaniesBundle:CompanyCategory');
        $companyRepository = $em->getRepository('MetalCompaniesBundle:Company');

        // Отключаем все города компаниям
        $affected = $this->companyCityRepository->disableCompanyCities();
        $output->writeln(sprintf('%s: Disable %d offices for all companies.', date('d.m.Y H:i:s'), $affected));

        $citiesCountPerPackage = PackageChecker::getOptionValuesPerPackage('max_cities_count');
        foreach ($citiesCountPerPackage as $packageId => $citiesCount) {
            $companiesIds = $companyRepository->getCompaniesIdsByCodeAccess($packageId);
            $affected = $this->enableCompanyCitiesByLimit($companiesIds, $citiesCount);
            $output->writeln(sprintf('%s: Enabled %d offices for companies of the second package.', date('d.m.Y H:i:s'), $affected));
        }
        
        $minId = $this->connection->fetchColumn('SELECT MIN(Message_ID) FROM Message75');
        $maxId = $this->connection->fetchColumn('SELECT MAX(Message_ID) FROM Message75');
        $idFrom = $minId;
        do {
            $idTo = $idFrom + 1000;
            $companies = $companyRepository->createQueryBuilder('company')
                ->where('company.id >= :id_from')
                ->setParameter('id_from', $idFrom)
                ->andWhere('company.id < :id_to')
                ->setParameter('id_to', $idTo)
                ->getQuery()
                ->getResult()
            ;
            /* @var $companies Company[] */

            foreach ($companies as $key => $company) {
                $output->writeln(sprintf('%s: Refresh enable companyCategories for company %d.', date('d.m.Y H:i:s'), $company->getId()));
                $companyCategoryRepository->disableCompanyCategories($company->getId());
                $companyCategoryRepository->enableCompanyCategoriesByLimit($company->getId(), $company->getPackageChecker()->getMaxPossibleCategoriesCount());
                unset($companies[$key]);
            }

            $idFrom = $idTo;
        } while ($idFrom <= $maxId);

        $refreshCompanyTitlesCommand = $this->getApplication()->find('metal:companies:refresh-company-titles');
        $refreshCompanyTitlesCommand->run(
            new ArrayInput(
                array(
                    'command' => 'metal:companies:refresh-company-titles',
                    '--refresh' => array('cities', 'categories')
                )),
            $output
        );

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }

    /**
     * @param array $companiesIds
     * @param $limit
     *
     * @return int
     */
    private function enableCompanyCitiesByLimit(array $companiesIds, $limit)
    {
        $affectedRows = 0;
        foreach ($companiesIds AS $companyId) {
            $affectedRows += $this
                ->companyCityRepository->enableCompanyCitiesByLimit($companyId, $limit);
        }

        return $affectedRows;
    }
}
