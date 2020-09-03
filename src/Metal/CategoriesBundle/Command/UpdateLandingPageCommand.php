<?php

namespace Metal\CategoriesBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Exporter\Writer\CsvWriter;
use Metal\CategoriesBundle\Entity\LandingPage;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFacetSpec;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\ProjectBundle\Util\InsertUtil;
use Metal\TerritorialBundle\Entity\Country;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateLandingPageCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:categories:update-landing-page');
        $this->addOption('landing-id', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY);
        $this->addOption('export', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $conn = $em->getConnection();
        /* @var $conn Connection */

        $landingPagesIds = $input->getOption('landing-id');
        $landingPageRepository = $em->getRepository('MetalCategoriesBundle:LandingPage');
        $countryRepository = $em->getRepository('MetalTerritorialBundle:Country');
        $cityRepository = $em->getRepository('MetalTerritorialBundle:City');
        $router = $this->getContainer()->get('router');

        $landingPageRepository->resetCounters($landingPagesIds);

        $criteria = array('enabled' => true);
        if ($landingPagesIds) {
            $criteria['id'] = $landingPagesIds;
        }
        $landingPages = $landingPageRepository->findBy($criteria);
        /* @var $landingPages LandingPage[] */

        $urls = array();
        $hostnamesMap = $this->getContainer()->getParameter('hostnames_map');

        foreach ($landingPages as $landingPage) {
            $id = $landingPage->getId();
            $specification = $landingPage->getProductFilteringSpec();

            $country = $landingPage->getCountry();
            $region = $landingPage->getRegion();
            $city = $landingPage->getCity();

            $subdomain = 'www';
            $baseHost = $this->getContainer()->getParameter('base_host');

            if ($city) {
                $subdomain = $city->getSlug();
                if (!$subdomain) {
                    continue;
                }
                $baseHost = $city->getDisplayInCountry()->getBaseHost();
            } elseif ($region) {
                $subdomain = $region->getSlug();
                $baseHost = $region->getAdministrativeCenter()->getDisplayInCountry()->getBaseHost();
            } elseif ($country) {
                if (isset($hostnamesMap[$country->getBaseHost()])) {
                    $baseHost = $country->getBaseHost();
                }
            }

            $dataFetcher = $this->getContainer()->get('metal.products.data_fetcher');
            $loadedProductsCount = 0;
            $countryDataToInsert = array();

            // строим фасет по странам
            if (!$landingPage->isModeSelectedTerritory()) {
                $facetSpec = new ProductsFacetSpec();
                $facetSpec->facetByCountries($specification);
                $facetsResultSet = $dataFetcher->getFacetedResultSetByCriteria($specification, $facetSpec, false);

                $allCountries = (new FacetResultExtractor(
                    $facetsResultSet, ProductsFacetSpec::COLUMN_COUNTRIES_IDS
                ))->getCounts();
                $countries = array_intersect_key($allCountries, array_flip(Country::getEnabledCountriesIds()));

                foreach ($countries as $countryId => $count) {
                    if ($count >= LandingPage::MIN_PRODUCTS_COUNT) {
                        $country = $countryRepository->find((int)$countryId);
                        $baseHost = $country->getBaseHost();

                        $countryDataToInsert[] = array(
                            'landing_page_id' => $id,
                            'country_id' => $country->getId(),
                            'results_count' => $count,
                        );

                        $urls[] = array(
                            'url' => $router->generate(
                                'MetalCategoriesBundle:LandingPage:landing',
                                array(
                                    'subdomain' => $subdomain,
                                    'base_host' => $baseHost,
                                    'slug' => $landingPage->getSlug(),
                                ),
                                true
                            ),
                            'title' => $landingPage->getTitle(),
                            'count' => $count,
                        );
                    }
                }
            }

            // reset results count
            $conn->update('landing_page_city_count', array('results_count' => 0), array('landing_page_id' => $id));
            $conn->update('landing_page_country_count', array('results_count' => 0), array('landing_page_id' => $id));
            $cityDataToInsert = array();

            if ($landingPage->isVisibleEverywhere()) {
                $facetSpecAnywhere = new ProductsFacetSpec();
                $facetSpecAnywhere->facetByCompanyCity($specification);
                $facetsResultSet = $dataFetcher->getFacetedResultSetByCriteria($specification, $facetSpecAnywhere, false);

                $resultsCountPerCities = (new FacetResultExtractor($facetsResultSet, ProductsFacetSpec::COLUMN_COMPANY_CITY_ID))
                    ->getCounts();

                foreach ($resultsCountPerCities as $cityId => $count) {
                    if ($count < LandingPage::MIN_PRODUCTS_COUNT) {
                        continue;
                    }

                    $city = $cityRepository->find((int)$cityId);
                    // к нам в результаты попадает виртуальный город, которого естественно нет в базе
                    if (!$city) {
                        continue;
                    }

                    if (!in_array($city->getCountry()->getId(), Country::getEnabledCountriesIds())) {
                        continue;
                    }

                    $subdomain = $city->getSlug();
                    if (!$subdomain) {
                        continue;
                    }
                    $baseHost = $city->getDisplayInCountry()->getBaseHost();
                    $cityDataToInsert[] = array(
                        'landing_page_id' => $id,
                        'city_id' => $city->getId(),
                        'results_count' => $count,
                    );
                    $urls[] = array(
                        'url' => $router->generate(
                            'MetalCategoriesBundle:LandingPage:landing',
                            array(
                                'subdomain' => $subdomain,
                                'base_host' => $baseHost,
                                'slug' => $landingPage->getSlug(),
                            ),
                            true
                        ),
                        'title' => $landingPage->getTitle(),
                        'count' => $count,
                    );
                }
            }

            if ($landingPage->isModeSelectedTerritory()) {
                $loadedProductsCount = $dataFetcher->getItemsCountByCriteria($specification, false);

                if ($loadedProductsCount >= LandingPage::MIN_PRODUCTS_COUNT) {
                    $urls[] = array(
                        'url' => $router->generate(
                            'MetalCategoriesBundle:LandingPage:landing',
                            array(
                                'subdomain' => $subdomain,
                                'base_host' => $baseHost,
                                'slug' => $landingPage->getSlug(),
                            ),
                            true
                        ),
                        'title' => $landingPage->getTitle(),
                        'count' => $loadedProductsCount,
                    );
                }
            }

            $landingPage->setResultsCount($loadedProductsCount);
            $landingPage->setResultsCountUpdatedAt(new \DateTime());

            $em->flush($landingPage);

            if ($specification->cityId && $landingPage->getCity()) {
                $cityDataToInsert[] = array(
                    'landing_page_id' => $id,
                    'city_id' => $specification->cityId,
                    'results_count' => $loadedProductsCount,
                );
            }

            if ($specification->countryId && $landingPage->getCountry()) {
                $countryDataToInsert[] = array(
                    'landing_page_id' => $id,
                    'country_id' => $specification->countryId,
                    'results_count' => $loadedProductsCount,
                );
            }

            if ($cityDataToInsert) {
                InsertUtil::insertMultipleOrUpdate($conn, 'landing_page_city_count', $cityDataToInsert, array('results_count'));
            }

            if ($countryDataToInsert) {
                InsertUtil::insertMultipleOrUpdate($conn, 'landing_page_country_count', $countryDataToInsert, array('results_count'));
            }
        }

        if ($input->getOption('export')) {
            $this->exportToCsv($urls);
        }

        $output->writeln(sprintf('Done command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }

    /**
     * @param array $elements
     */
    private function exportToCsv($elements)
    {
        $dir = $this->getContainer()->getParameter('web_dir');
        $filename = $dir.'/landing_page.csv';
        if (file_exists($filename)) {
            unlink($filename);
        }
        $csvExport = new CsvWriter($filename, ';');
        $csvExport->open();
        foreach ($elements as $element) {
            $csvExport->write($element);
        }
        $csvExport->close();
    }
}
