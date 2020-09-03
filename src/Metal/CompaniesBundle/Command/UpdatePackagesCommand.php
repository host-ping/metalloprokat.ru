<?php

namespace Metal\CompaniesBundle\Command;

use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\PackageChecker;
use Metal\CompaniesBundle\Repository\CompanyCityRepository;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditChangeSet;
use Metal\ServicesBundle\Entity\Package;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\LockHandler;

class UpdatePackagesCommand extends ContainerAwareCommand
{
    const UPDATE_LIMIT = 250;

    protected function configure()
    {
        $this->setName('metal:companies:update-packages');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $conn = $this->getContainer()->get('doctrine')->getConnection();
        $conn->getConfiguration()->setSQLLogger(null);

        $minId = $conn->fetchColumn('SELECT MIN(Message_ID) FROM Message75');
        $maxId = $conn->fetchColumn('SELECT MAX(Message_ID) FROM Message75');

        $lock = new LockHandler($this->getName());
        if (!$lock->lock()) {
            $output->writeln(sprintf('The command "%s" is already running in another process.', $this->getName()));

            return 0;
        }

        $i = 0;
        $idFrom = $minId;
        do {
            $idTo = $idFrom + self::UPDATE_LIMIT;
            $this->processCompanies($idFrom, $idTo, $output);
            $idFrom = $idTo;
            $i++;
            if ($i % 50 == 0) {
                $output->writeln($idTo.' / '.$maxId.' '.date('d.m.Y H:i:s'));
            }
        } while ($idFrom <= $maxId);

        $lock->release();

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }

    private function processCompanies($idFrom, $idTo, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine.orm.default_entity_manager');
        $conn = $em->getConnection();
        //FIXME: нужно использовать какую-то специальную колонку для отслеживания изменения visibility_status и не обращаться в сфинкс
        $sphinxy = $container->get('sphinxy.default_connection');

        $companies = $conn->fetchAll(
            'SELECT Message_ID, code_access FROM Message75 WHERE Message_ID >= :id_from AND Message_ID < :id_to AND deleted_at_ts = 0',
            array(
                'id_from' => $idFrom,
                'id_to' => $idTo,
            )
        );

        if (!$companies) {
            return;
        }

        $originalCodeAccess = array_column($companies, 'code_access', 'Message_ID');

        $companiesFromSphinx = $sphinxy
            ->createQueryBuilder()
            ->select('company_id, visibility_status')
            ->from('products')
            ->where('company_id IN :companies_ids')
            ->setParameter('companies_ids', array_column($companies, 'Message_ID'))
            ->groupBy('company_id')
            ->setMaxResults(self::UPDATE_LIMIT)
            ->getResult();

        $companiesFromSphinx = iterator_to_array($companiesFromSphinx);
        $originalVisibilityStatus = array_column($companiesFromSphinx, 'visibility_status', 'company_id');
        $emptyVisibilityStatus = array_fill_keys(array_keys($originalCodeAccess), 0);
        $originalVisibilityStatus = array_replace($emptyVisibilityStatus, $originalVisibilityStatus);

        $output->writeln(sprintf('%s: Reset code access for companies.', date('d.m.Y H:i:s')));
        $conn->executeUpdate('
            UPDATE Message75 c
            SET c.code_access = :code
            WHERE c.Message_ID >= :id_from AND c.Message_ID < :id_to AND c.deleted_at_ts = 0',
            array(
                'code' => Package::BASE_PACKAGE,
                'id_from' => $idFrom,
                'id_to' => $idTo
            )
        );

        $output->writeln(sprintf('%s: Update code access for companies.', date('d.m.Y H:i:s')));
        $conn->executeUpdate('
        UPDATE Message75 c
        JOIN Message106 p ON p.company_id = c.Message_ID
        SET c.code_access = p.package_id
        WHERE :now BETWEEN p.start_date AND p.end_date AND c.Message_ID >= :id_from AND c.Message_ID < :id_to AND c.deleted_at_ts = 0',
            array(
                'now' => new \DateTime(),
                'id_from' => $idFrom,
                'id_to' => $idTo
            ),
            array(
                'now' => 'datetime'
            )
        );

        $output->writeln(sprintf('%s: Reset visibility status for companies.', date('d.m.Y H:i:s')));
        $conn->executeUpdate('
            UPDATE Message75 c
            SET c.visibility_status = :status
            WHERE c.Message_ID >= :id_from AND c.Message_ID < :id_to AND c.code_access = :code AND c.deleted_at_ts = 0',
            array(
                'code' => Package::BASE_PACKAGE,
                'status' => Company::VISIBILITY_STATUS_NORMAL,
                'id_from' => $idFrom,
                'id_to' => $idTo
            )
        );

        $companies = $conn->fetchAll(
            'SELECT 
              code_access, visibility_status, Message_ID AS company_id 
              FROM Message75 WHERE deleted_at_ts = 0
              AND Message_ID >= :id_from AND Message_ID < :id_to',
            array(
                'id_from' => $idFrom,
                'id_to' => $idTo,
            )
        );

        $updatedCodeAccess = array_diff_assoc(array_column($companies, 'code_access', 'company_id'), $originalCodeAccess);
        $updatedVisibilityStatus  = array_diff_assoc(array_column($companies, 'visibility_status', 'company_id'), $originalVisibilityStatus);

//        if ($updatedCodeAccess || $updatedVisibilityStatus) {
//            dump($updatedCodeAccess);
//            dump($updatedVisibilityStatus);
//            exit;
//        }

        /** @var CompanyCityRepository $companyCityRepository */
        $companyCityRepository = $em->getRepository('MetalCompaniesBundle:CompanyCity');

        if ($updatedCodeAccess) {
            $output->writeln(sprintf('%s: Disable company cities', date('d.m.Y H:i:s')));
            $affected = $companyCityRepository->disableCompanyCities(array_keys($updatedCodeAccess));
            $output->writeln(sprintf('%s: Disabled cities %d', date('d.m.Y H:i:s'), $affected));
        }

        $httpsAvailablePerPackage = PackageChecker::getOptionValuesPerPackage('https_allowed');
        $maxCitiesCountPerPackage = PackageChecker::getOptionValuesPerPackage('max_cities_count');

        $toRemoveCloudflareCompaniesIds = array();
        $toAddCloudflareCompaniesIds = array();
        foreach ($updatedCodeAccess as $companyId => $codeAccess) {
            $limit = $maxCitiesCountPerPackage[$codeAccess];
            $affected = $companyCityRepository->enableCompanyCitiesByLimit($companyId, $limit);
            $output->writeln(sprintf('%s: Company %d enabled %d cities. Maximum: %d', date('d.m.Y H:i:s'), $companyId, $affected, $limit));

            if ($httpsAvailablePerPackage[$codeAccess]) {
                $toAddCloudflareCompaniesIds[] = $companyId;
            } else {
                $toRemoveCloudflareCompaniesIds[] = $companyId;
            }
        }

        if ($updatedCodeAccess) {
            $companiesIds = array_keys($updatedCodeAccess);

            $this->getApplication()->find('metal:companies:refresh-company-titles')->run(
                new ArrayInput(
                    array(
                        'command' => 'metal:companies:refresh-company-titles',
                        '--refresh' => array('cities'),
                        '--company-id' => $companiesIds,
                    )
                ),
                $output
            );

            $output->writeln(sprintf('%s: Update companies allowed products count."', date('Y-m-d H:i')));
            $this->getApplication()->find('metal:products:update-allowed-add-count-products')->run(
                new ArrayInput(
                    array(
                        'command' => 'metal:products:update-allowed-add-count-products',
                        '--company-id' => $companiesIds,
                    )
                ),
                $output
            );

            $output->writeln(sprintf('%s: Update companies in cloudflare"', date('Y-m-d H:i')));
            $cloudflareService = $container->get('metal.project.cloudflare_service');
            $countryId = $container->getParameter('hostnames_map')[$container->getParameter('base_host')]['country_id'];
            $companyRepository = $em->getRepository('MetalCompaniesBundle:Company');

            $companiesSlugs = $companyRepository
                ->getCloudflareSlugsForCountryByIds($countryId, $toRemoveCloudflareCompaniesIds);
            $logger = function ($record, $success, $errorMsg) use ($output, $companyRepository) {
                if ($success) {
                    $output->writeln(sprintf('Record "%s" removed.', $record));
                    $companyRepository->updateCompanyIsAddedToCloudflareStatus($record, false);
                } else {
                    $output->writeln(sprintf('Record "%s" failed ("%s").', $record, $errorMsg));
                }
            };

            $cloudflareService->removeRecords($companiesSlugs, $logger);

            $companiesSlugs = $companyRepository
                ->getCloudflareSlugsForCountryByIds($countryId, $toAddCloudflareCompaniesIds);
            $logger = function ($record, $success, $errorMsg) use ($output, $companyRepository) {
                if ($success) {
                    $output->writeln(sprintf('Record "%s" added.', $record));
                    $companyRepository->updateCompanyIsAddedToCloudflareStatus($record, true);
                } else {
                    $output->writeln(sprintf('Record "%s" failed ("%s").', $record, $errorMsg));
                }
            };

            $cloudflareService->insertRecords($companiesSlugs, $logger);
        }

        if ($updatedVisibilityStatus || $updatedCodeAccess) {
            $companiesToUpdate = array_keys(array_replace($updatedCodeAccess, $updatedVisibilityStatus));

            //FIXME: разобраться, что тут происходит. Почему-то постоянно удаляются виртуальные товары
            $output->writeln(sprintf('%s: Get products ids for reindex', date('d.m.Y H:i:s')));
            $products = $companyCityRepository->getProductsIdsForReindex($companiesToUpdate);
            $output->writeln(sprintf('%s: Get products ids for remove', date('d.m.Y H:i:s')));
            $productsToRemove = $companyCityRepository->getProductsIdsForRemove($companiesToUpdate);

            if ($products || $productsToRemove) {
                $productsChangeSet = new ProductsBatchEditChangeSet();
                $productsChangeSet->productsToDisable = $productsToRemove;
                $productsChangeSet->productsToEnable = $products;

                $output->writeln(sprintf('%s: Send consumer.', date('d.m.Y H:i:s')));
                $container->get('sonata.notification.backend')
                    ->createAndPublish('admin_products', array('changeset' => $productsChangeSet));
            }
        }
    }
}
