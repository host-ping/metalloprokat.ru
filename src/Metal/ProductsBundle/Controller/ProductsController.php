<?php

namespace Metal\ProductsBundle\Controller;

use Metal\CategoriesBundle\Entity\Category;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Helper\DefaultHelper;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Result\ProductItem;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsLoadingSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsOrderingSpec;
use Metal\ProjectBundle\DataFetching\CacheProfile;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\Entity\ValueObject\SourceTypeProvider;
use Metal\ServicesBundle\Entity\Package;
use Metal\StatisticBundle\Entity\StatsSearch;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\Region;
use Metal\TerritorialBundle\Entity\TerritoryInterface;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductsController extends Controller
{
    const MIN_PRODUCTS_COUNT = 5;
    const MIN_COMPANIES_COUNT = 5;

    public function searchAction(Request $request, City $city = null, Region $region = null)
    {
        $query = $request->query->get('query');
        $category = null;
        $categoryId = $request->query->get('category');

        $subdomainSlug = 'www';
        if ($city) {
            $subdomainSlug = $city->getSlugWithFallback();
        }
        if ($region) {
            $subdomainSlug = $region->getSlug();
        }

        if ($categoryId) {
            $category = $this->getDoctrine()->getManager()->find('MetalCategoriesBundle:Category', $categoryId);
        }

        if (!$query) {
            if ($category) {
                $routeParameters = array(
                    'category_slug' => $category->getSlugCombined(),
                );

                if ($categoryId) {
                    $routeParameters['context'] = 1;
                }

                return $this->redirect(
                    $this->generateUrl('MetalStatisticBundle:Default:redirectStats',
                        array(
                            'kind' => StatsSearch::SEARCH_BY_BUTTON,
                            'url' => $this->generateUrl('MetalProductsBundle:Products:list_category', $routeParameters)
                        )
                    )
                );
            }

            return $this->redirect(
                $this->generateUrl('MetalStatisticBundle:Default:redirectStats',
                    array(
                        'kind' => StatsSearch::SEARCH_BY_BUTTON,
                        'url' => $this->generateUrl('MetalProductsBundle:Products:products_list', array('subdomain' => $subdomainSlug)
                        )
                    )
                )
            );
        }

        $attributesCollection = null;
        $categoryService = $this->get('metal.categories.category_matcher');
        $activeCategory = null;

        if ($category) {
            $activeCategory = $category;
        } else {
//            $activeCategory = $categoryService->getCategoryByTitle($query);
            $activeCategory = null;
        }

        $specification = new ProductsFilteringSpec();
        if ($activeCategory) {

            $specification->category($activeCategory);
            $productParameterValueRepository = $this->get('doctrine')->getRepository('MetalProductsBundle:ProductParameterValue');

            $parametersSlugs = $productParameterValueRepository->matchAttributesForTitle($activeCategory->getId(), $query);
            if ($parametersSlugs) {

                $attributesCollection = $this->getDoctrine()->getRepository('MetalAttributesBundle:AttributeValue')
                    ->loadCollectionBySlugs($activeCategory, array_keys($parametersSlugs));

            }
        }

        $specification->matchTitle($query);

        if ($attributesCollection) {
            $specification->attributesCollection($attributesCollection);
        }

        $dataFetcher = $this->get('metal.products.data_fetcher');
        $productsCount = $dataFetcher->getItemsCountByCriteria($specification);

        $specification
            ->loadCompanies(true)
            ->allowVirtual(true)
        ;

        $companiesCount = $dataFetcher->getItemsCountByCriteria($specification);
        if (!$productsCount && !$companiesCount && $this->container->getParameter('project.catalog_enabled')) {
            return $this->redirect(
                $this->generateUrl('MetalCatalogBundle:Search:search',
                    array(
                        'q' => $query
                    )
                )
            );
        }

        if ($productsCount < $companiesCount) {
            if ($activeCategory) {
                $routeParameters = array(
                    'subdomain' => $subdomainSlug,
                    'category_slug' => $attributesCollection ? $activeCategory->getUrl($attributesCollection->getUrl()) : $activeCategory->getSlugCombined(),
                    'q' => $query
                );

                if ($categoryId) {
                    $routeParameters['context'] = 1;
                }

                return $this->redirect(
                    $this->generateUrl('MetalStatisticBundle:Default:redirectStats',
                        array(
                            'kind' => StatsSearch::SEARCH_BY_BUTTON,
                            'url' => $this->generateUrl('MetalProductsBundle:Products:companies_list_category_subdomain', $routeParameters)
                        )
                    )
                );
            }

            $routeParameters = array(
                'subdomain' => $subdomainSlug,
                'q' => $query
            );

            if ($categoryId) {
                $routeParameters['context'] = 1;
            }

            return $this->redirect(
                $this->generateUrl('MetalStatisticBundle:Default:redirectStats',
                    array(
                        'kind' => StatsSearch::SEARCH_BY_BUTTON,
                        'url' => $this->generateUrl('MetalProductsBundle:Products:companies_list', $routeParameters)
                    )
                )
            );
        }

        $routeParameters = array(
            'subdomain' => $subdomainSlug,
            'q' => $query,
        );

        if ($activeCategory) {
            $routeParameters['category_slug'] = $attributesCollection ? $activeCategory->getUrl($attributesCollection->getUrl()) : $activeCategory->getSlugCombined();

            if ($categoryId) {
                $routeParameters['context'] = 1;
            }

            return $this->redirect(
                $this->generateUrl('MetalStatisticBundle:Default:redirectStats',
                    array(
                        'kind' => StatsSearch::SEARCH_BY_BUTTON,
                        'url' => $this->generateUrl('MetalProductsBundle:Products:list_category_subdomain', $routeParameters)
                    )
                )
            );
        }

        if ($categoryId) {
            $routeParameters['context'] = 1;
        }

        return $this->redirect(
            $this->generateUrl('MetalStatisticBundle:Default:redirectStats',
                array(
                    'kind' => StatsSearch::SEARCH_BY_BUTTON,
                    'url' => $this->generateUrl('MetalProductsBundle:Products:products_list', $routeParameters)
                )
            )
        );
    }

    public function listAction(Request $request, TerritoryInterface $territory, Country $country, Category $category = null, $sort = true)
    {
        if ($request->query->get('tab') === 'companies') {
            $companiesListRoute = $category ? 'MetalProductsBundle:Products:companies_list_category' : 'MetalProductsBundle:Products:companies_list';

            return new RedirectResponse(
                $this->generateUrl(
                    $companiesListRoute,
                    array_merge($request->attributes->get('_route_params'), $request->query->all())
                ), 301
            );
        }

        if ($request->query->get('view') === 'map') {
            return $this->mapView($request, $territory, $category);
        }

        $page = $request->query->get('page', 1);
        $isPalette = $request->query->get('view') === 'pallete';
        $perPage = $isPalette ? 21 : 20;

        $specification = ProductsFilteringSpec::createFromRequest($request)
            ->loadProductsCountPerCompany(true)
            ->loadSpecialOfferProduct(true);

        $loaderOpts = (new ProductsLoadingSpec())
            ->preloadDelivery($territory)
            ->preloadPhones($territory)
            ->trackShowing(SourceTypeProvider::PRODUCTS)
            ->normalizePrice($country)
        ;

        $orderBy = new ProductsOrderingSpec();

        if ($sort) {
            $orderBy
                ->payedCompanies()
                ->iterateByCompany();

            if (!$orderBy->applyFromRequest($request)) {
                $orderBy->rating();
            }

            if (!$category || !$category->getRealCategory()) {
                $orderBy->companyLastVisitedAt();
            }

            $orderBy->specialOffer();
        } else {
            $orderBy->createdAt();
        }

        //TODO: сортировка по рандому не работает #MET-1696, всему виной все тот же http://sphinxsearch.com/bugs/view.php?id=2009
        //if ($city) {
        //    $orderBy->random($city->getId());
        //} elseif ($region) {
        //    $orderBy->random($region->getId());
        //}

        $dataFetcher = $this->get('metal_products.data_fetcher_factory')->getDataFetcher(ProductIndex::SCOPE);
        $productsEntityLoader = $this->get('metal.products.products_entity_loader');

        $productsPagerfanta = $dataFetcher->getPagerfantaByCriteria($specification, $orderBy, $perPage, $page, DataFetcher::TTL_5MINUTES);

        if ($request->isXmlHttpRequest()) {
            $response = JsonResponse::create(
                array(
                    //TODO: тут желательно передавать виджет с табами
                    'page.title' => $this->get('brouzie.helper_factory')->get('MetalProductsBundle:ProductsListSeo')->getMetaTitleForCategoryPage($category),
                    'page.products_list' => $this->renderView(
                        'MetalProductsBundle:Products:partial/list_products.html.twig',
                        array(
                            'pagerfanta' => $productsEntityLoader->getPagerfantaWithEntities($productsPagerfanta, $loaderOpts),
                            'category' => $category
                        )
                    )
                )
            );

            $response->headers->addCacheControlDirective('no-store');

            return $response;
        }

        $productsViewModel = $productsEntityLoader->getListResultsViewModel($productsPagerfanta, $loaderOpts);
        // сначала грузим текущую страницу а потом уже вызываем запрос на кол-во, таким образом экономим 1 запрос
        $loadedProductsCount = $productsPagerfanta->getNbResults();

        $fallbackProductsViewModel = null;
        if ($page == 1 && $loadedProductsCount < self::MIN_PRODUCTS_COUNT
            && ($specification->companyAttrs || $specification->productAttrsByGroup || $specification->hasTerritoryFilters())) {
            $fallbackSpecification = clone $specification;
            //TODO: сделать сортировку по кол-ву совпавших атрибутов
            $fallbackSpecification->companyAttrs = null;
            $fallbackSpecification->resetAttributesFilter();
            $fallbackSpecification->resetTerritoryFilters();
            $fallbackSpecification->exceptProducts($productsViewModel->pagerfanta);
            $fallbackOrderBy = clone $orderBy;
            $fallbackOrderBy->random();
            //FIXME: кеширование fallback-критерии не работает по причине exceptProducts, нужно выбирать на count(getCurrentPageResults) больше результатов и в пхп их убирать
            $fallbackProductsPagerfanta = $dataFetcher->getPagerfantaByCriteria(
                $fallbackSpecification,
                $fallbackOrderBy,
                $perPage - $loadedProductsCount,
                $page,
                DataFetcher::TTL_1HOUR
            );
            $fallbackProductsViewModel = $productsEntityLoader->getListResultsViewModel($fallbackProductsPagerfanta, $loaderOpts);
        }

        $template = '@MetalProducts/Products/list.html.twig';
        if ($isPalette) {
            $template = '@MetalProducts/Products/list_pallete.html.twig';
        }

        $randomPremiumProductsPagerfanta = null;
        if ($page == 1 && !$isPalette && $this->container->getParameter('project.family') !== 'product') {
            if ($territory instanceof City){
                $randomPremiumProductsPagerfanta = $this->getRandomPremiumProducts($specification, $loaderOpts, $orderBy);
            }
        }

        return $this->render($template, array(
            'productsViewModel' => $productsViewModel,
            'fallbackProductsViewModel' => $fallbackProductsViewModel,
            'randomPremiumProductsPagerfanta' => $randomPremiumProductsPagerfanta,
            'category' => $category,
        ));
    }

    public function listCompaniesAction(Request $request, TerritoryInterface $territory, Category $category = null, $sort = true)
    {
        $queryBag = $request->query;

        if ($queryBag->get('view') === 'map') {
            return $this->mapView($request, $territory, $category);
        }

        if ($request->query->has('category_slug')) {
            $request->query->remove('category_slug');

            return new RedirectResponse(
                $this->generateUrl(
                    $request->attributes->get('_route'),
                    array_merge($request->attributes->get('_route_params'), $request->query->all())
                ), 301
            );
        }

        $perPage = 20;
        $page = $queryBag->get('page', 1);

        $orderBy = new ProductsOrderingSpec();

        if ($sort) {
            $orderBy->payedCompanies();
            if ($queryBag->get('q')) {
                $orderBy->weight();
            }

            if (!$orderBy->applyFromRequest($request, false)) {
                $orderBy->rating();
            }

            if (!$category || !$category->getRealCategory()) {
                $orderBy->companyLastVisitedAt();
            }

            $orderBy->specialOffer();
        } else {
            $orderBy->companyCreatedAt();
        }

        $dataFetcher = $this->get('metal.products.data_fetcher');
        $companiesEntityLoader = $this->get('metal.products.companies_entity_loader');

        $companiesSpecification = ProductsFilteringSpec::createFromRequest($request);;
        $companiesSpecification
            ->allowVirtual(true)
            ->loadCompanies(true);

        $companiesViewModel = null;
//        try {
            $companiesPagerfanta = $dataFetcher->getPagerfantaByCriteria($companiesSpecification, $orderBy, $perPage, $page, DataFetcher::TTL_5MINUTES);
//        } catch (ConnectionException $e) {
//            if (preg_match('#^offset out of bounds .+ max_matches=(\d+)#ui', $e->getMessage(), $matches)) {
//                $routeParam = array_merge($request->attributes->get('_route_params'), $request->query->all());
//                $routeParam['page'] = $matches[1] / $perPage;
//                $url = $this->generateUrl(
//                    $request->attributes->get('_route'),
//                    $routeParam
//                );
//
//                return $this->redirect($url, 301);
//            } else {
//                throw $e;
//            }
//        }

        $loaderOpts = (new ProductsLoadingSpec())
            ->preloadDelivery($territory)
            ->preloadPhones($territory);

        if (!$category) {
            $loaderOpts->attachProductsAttr(true, $request->query->get('q'));
        }

        if ($request->isXmlHttpRequest()) {
            $response = JsonResponse::create(
                array(
                    'page.title' => $this->get('brouzie.helper_factory')->get('MetalCompaniesBundle:CompaniesListSeo')->getMetaTitleForCompanyCatalogPage(),
                    'page.companies_list' => $this->renderView(
                        '@MetalProducts/Companies/list_companies.html.twig',
                        array(
                            'pagerfanta' => $companiesEntityLoader->getPagerfantaWithEntities($companiesPagerfanta, $loaderOpts),
                            'category' => $category,
                        )
                    )
                )
            );

            $response->headers->addCacheControlDirective('no-store', true);

            return $response;
        }

        $companiesViewModel = $companiesEntityLoader->getListResultsViewModel($companiesPagerfanta, $loaderOpts);

        $loadedCompaniesCount = $companiesPagerfanta->getNbResults();

        $fallbackCompaniesViewModel = null;
        if ($page == 1 && $loadedCompaniesCount < self::MIN_COMPANIES_COUNT
            && ($companiesSpecification->companyAttrs || $companiesSpecification->productAttrsByGroup || $companiesSpecification->cityId)) {
            $fallbackSpecification = clone $companiesSpecification;
            //TODO: сделать сортировку по кол-ву совпавших атрибутов
            $fallbackSpecification->companyAttrs = null;
            $fallbackSpecification->resetAttributesFilter();
            $fallbackSpecification->cityId = null;
            $fallbackSpecification->exceptCompanies($companiesViewModel->pagerfanta);
            $fallbackOrderBy = clone $orderBy;
            $fallbackOrderBy->random();
            //FIXME: кеширование fallback-критерии не работает по причине exceptCompanies, см аналогичный fixme для товаров
            $fallbackCompaniesPagerfanta = $dataFetcher->getPagerfantaByCriteria($fallbackSpecification, $fallbackOrderBy, $perPage - $loadedCompaniesCount, $page, DataFetcher::TTL_1HOUR);
            $fallbackCompaniesViewModel = $companiesEntityLoader->getListResultsViewModel($fallbackCompaniesPagerfanta, $loaderOpts);
        }

        return $this->render(
            '@MetalProducts/Companies/list.html.twig', array(
                'companiesViewModel' => $companiesViewModel,
                'fallbackCompaniesViewModel' => $fallbackCompaniesViewModel,
                'category' => $category
            ));
    }

    private function mapView(Request $request, TerritoryInterface $territory, Category $category = null)
    {
        $specification = ProductsFilteringSpec::createFromRequest($request)
            ->onlyPriorityShowCompanies()
            ->allowVirtual(true)
            ->loadCompanies(true);

        $loaderOpts = (new ProductsLoadingSpec())
            ->preloadDelivery($territory)
            ->preloadPhones($territory);

        $limit = 2000;
        $dataFetcher = $this->get('metal.products.data_fetcher');
        $companiesEntityLoader = $this->get('metal.products.companies_entity_loader');

        //TODO: сделать кеширование на 6 часов
        $pagerfanta = $dataFetcher->getPagerfantaByCriteria($specification, null, $limit);
        $companiesViewModel = $companiesEntityLoader->getListResultsViewModel($pagerfanta, $loaderOpts);

        return $this->render('@MetalProducts/Companies/listOnMap.html.twig', array(
            'category' => $category,
            'companiesViewModel' => $companiesViewModel,
        ));
    }

    private function getRandomPremiumProducts(ProductsFilteringSpec $criteria, ProductsLoadingSpec $loaderOpts, ProductsOrderingSpec $orderBy)
    {
        //FIXME: добавить фильтр на уровне пхп, что бы сюда не грузились товары из основного запроса и не было подряд двух одних и тех же товаров http://msk.metalloprokat.dev/sort/armatura/8/at800/
        $dataFetcher = $this->get('metal_products.data_fetcher_factory')->getDataFetcher(ProductIndex::SCOPE);
        $entityLoader = $this->container->get('metal.products.products_entity_loader');

        $criteria = clone $criteria;
        /* @var $criteria ProductsFilteringSpec */
        // если была выбрана галочка "искать только в городе" - сбрасываем ее и ищем включая филиалы
        if ($criteria->companyCityId) {
            $criteria->cityId($criteria->companyCityId);
            $criteria->companyCityId(null);
        }

        $criteria->onlyPriorityShowCompanies();

        $key = CacheProfile::getKeyFromSpecifications(array($criteria), array(__METHOD__));
        // применяем эту спецификацию уже после того, как посчитали ключ кеша
        $criteria->loadRandomSpecialOfferProductPerCompany();

        $cacheProfile = $key ? new CacheProfile($key, DataFetcher::TTL_6HOURS) : null;
        $pagerfanta = $dataFetcher->getPagerfantaByCriteria($criteria, $orderBy, 400, 1, $cacheProfile);

        $productItems = iterator_to_array($pagerfanta);
        shuffle($productItems);

        $targetProductItems = [];
        $showedCompanies = [];
        /** @var ProductItem $productItem */
        foreach ($productItems as $productItem) {
            if (!isset($targetProductItems[0])) {
                // компания полного доступа из города
                if ($productItem->companyAccess == Package::FULL_PACKAGE
                    && $productItem->companyCityId == $criteria->cityId) {
                    $targetProductItems[0] = $productItem;
                    $showedCompanies[] = $productItem->companyId;
                    continue;
                }
            }

            if (!isset($targetProductItems[1])) {
                // платник из города
                if (!in_array($productItem->companyId, $showedCompanies) && $productItem->companyCityId == $criteria->cityId) {
                    $targetProductItems[1] = $productItem;
                    $showedCompanies[] = $productItem->companyId;
                    continue;
                }
            }

            if (!isset($targetProductItems[2])) {
                // платник из города
                if (!in_array($productItem->companyId, $showedCompanies) && $productItem->companyCityId == $criteria->cityId) {
                    $targetProductItems[2] = $productItem;
                    $showedCompanies[] = $productItem->companyId;
                    continue;
                }
            }

            if (!isset($targetProductItems[3])) {
                // полный+ или полный++
                if (!in_array($productItem->companyId, $showedCompanies) && $productItem->visibilityStatus == Company::VISIBILITY_STATUS_ALL_CITIES
                    || $productItem->visibilityStatus == Company::VISIBILITY_STATUS_ALL_COUNTRIES) {
                    $targetProductItems[3] = $productItem;
                    $showedCompanies[] = $productItem->companyId;
                    continue;
                }
            }

            if (isset($targetProductItems[0], $targetProductItems[1], $targetProductItems[2], $targetProductItems[3])) {
                break;
            }
        }

        ksort($targetProductItems);

        return $entityLoader->getPagerfantaWithEntities(
            new Pagerfanta(new FixedAdapter(count($targetProductItems), $targetProductItems)),
            $loaderOpts
        );
    }

    private function getRandomPremiumOnlineProducts(ProductsFilteringSpec $criteria, ProductsLoadingSpec $loaderOpts, ProductsOrderingSpec $orderBy)
    {
        //FIXME: добавить фильтр на уровне пхп, что бы сюда не грузились товары из основного запроса и не было подряд двух одних и тех же товаров http://msk.metalloprokat.dev/sort/armatura/8/at800/
        $dataFetcher = $this->get('metal_products.data_fetcher_factory')->getDataFetcher(ProductIndex::SCOPE);
        $entityLoader = $this->container->get('metal.products.products_entity_loader');

        $companyRepository = $this->getDoctrine()->getRepository('MetalCompaniesBundle:Company');
        /** @var  $companyHelper DefaultHelper*/
        $companyHelper = $this->get('brouzie.helper_factory')->get('MetalCompaniesBundle');

        $criteria = clone $criteria;
        /* @var $criteria ProductsFilteringSpec */
        // если была выбрана галочка "искать только в городе" - сбрасываем ее и ищем включая филиалы
        if ($criteria->companyCityId) {
            $criteria->cityId($criteria->companyCityId);
            $criteria->companyCityId(null);
        }

        $criteria->onlyPriorityShowCompanies();

        $key = CacheProfile::getKeyFromSpecifications(array($criteria), array(__METHOD__));
        // применяем эту спецификацию уже после того, как посчитали ключ кеша
        $criteria->loadRandomSpecialOfferProductPerCompany();

        $cacheProfile = $key ? new CacheProfile($key, DataFetcher::TTL_5MINUTES) : null;
        $pagerfanta = $dataFetcher->getPagerfantaByCriteria($criteria, $orderBy, 400, 1);

        $productItems = iterator_to_array($pagerfanta);
        shuffle($productItems);

        $targetProductItems = [];
        $showedCompanies = [];
        /** @var ProductItem $productItem */
        foreach ($productItems as $productItem) {
            if (count($targetProductItems) > 10) {
                break;
            }

            $company = $companyRepository->findOneBy(array('id' => $productItem->companyId));
            if (!in_array($productItem->companyId, $showedCompanies) && $companyHelper->isCompanyOnline($company)) {
            // компания полного доступа из города
                if ($productItem->companyAccess == Package::FULL_PACKAGE
                    && $productItem->companyCityId == $criteria->cityId
                ) {
                    if (isset($targetProductItems[0])) {
                        $targetProductItems[] = $targetProductItems[0];
                    }

                    $targetProductItems[0] = $productItem;
                    $showedCompanies[] = $productItem->companyId;
                    continue;
                }
                else {
                    $targetProductItems[] = $productItem;
                    $showedCompanies[] = $productItem->companyId;
                }
            }
        }

        ksort($targetProductItems);

        return $entityLoader->getPagerfantaWithEntities(
            new Pagerfanta(new FixedAdapter(count($targetProductItems), $targetProductItems)),
            $loaderOpts
        );
    }
}
