<?php

namespace Metal\ProductsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Metal\AttributesBundle\DataFetching\AttributesFacetResult;
use Metal\AttributesBundle\Entity\Attribute;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\LandingPage;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFacetSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\Entity\ProductImage;
use Metal\ProductsBundle\Helper\DefaultHelper;
use Metal\ProjectBundle\DataFetching\AdvancedDataFetcher;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\CountAggregationResult;
use Metal\ProjectBundle\Helper\ApiHelper;
use Metal\ProjectBundle\Helper\CounterHelper;
use Metal\ProjectBundle\Helper\ImageHelper;
use Metal\TerritorialBundle\Entity\Country;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{

    /**
     * @ParamConverter("category", class="MetalCategoriesBundle:Category", options={"id" = "category_id"})
     */
    public function getItemsCountAction(Request $request, Category $category, $tab)
    {
        $specification = ProductsFilteringSpec::createFromRequest($request);
        switch ($tab) {
            case 'companies':
                $route = 'MetalProductsBundle:Products:companies_list_category_subdomain';
                $specification
                    ->allowVirtual(true)
                    ->loadCompanies(true);
                break;

            case 'products':
            default :
//                $specification
//                    ->allowVirtual(true)
//                    ->loadCompanies(true);
                $route = 'MetalProductsBundle:Products:list_category_subdomain';
                break;
        }

        $dataFetcher = $this->container
            ->get('metal_products.data_fetcher_factory')
            ->getDataFetcher(ProductIndex::SCOPE);

        $count = $dataFetcher->getItemsCountByCriteria($specification);

        $attributesCollection = $this->getDoctrine()->getRepository('MetalAttributesBundle:AttributeValue')
            ->loadCollectionByGroups($specification->productAttrsByGroup);

        $queryBag = $request->query;

        $routeParams = array('category_slug' => $category->getUrl($attributesCollection->getUrl()));
        $routeParams['price_from'] = $queryBag->get('price_from');
        $routeParams['price_to'] = $queryBag->get('price_to');
        $routeParams['view'] = $queryBag->get('view');
        $routeParams['cattr'] = $queryBag->get('cattr');
        $routeParams['concrete_city'] = $queryBag->get('concrete_city');

        $requestRouteParams = $request->attributes->get('_route_params');
        unset($requestRouteParams['category_id']);
        $routeParams = array_merge($routeParams, $requestRouteParams);

        $routeParams = array_filter($routeParams);

        return JsonResponse::create(
            array(
                'count' => $count,
                'countFormatted' => $this->get('sonata.intl.templating.helper.number')->formatDecimal($count),
                'url' => $this->generateUrl($route, $routeParams)
            )
        );
    }

    public function territorialAction(Request $request, Country $country)
    {
        $requestBag = $request->request;
        $em = $this->getDoctrine()->getManager();
        $itemsType = $requestBag->get('counter_name');

        $apiHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Api');
        /* @var $apiHelper ApiHelper */
        $productsHelper = $this->container->get('brouzie.helper_factory')->get('MetalProductsBundle');
        /* @var $productsHelper DefaultHelper */

        $criteria = array();
        $filterParameters = array();
        $category = null;

        $activeCity = $request->attributes->get('city');
        $activeRegion = $request->attributes->get('region');
        $activeTerritory = null;
        if ($activeCity) {
            $activeTerritory = $activeCity;
        } elseif ($activeRegion) {
            $activeTerritory = $activeRegion;
        }

        $criteria['countries_ids'] = array($country->getId());
        $criteria['is_virtual'] = true;

        $routes = array(
            'products_list' => 'MetalProductsBundle:Products:products_list',
            'companies_list' => 'MetalProductsBundle:Products:companies_list',
            'last_products_list' => 'MetalProductsBundle:Products:products_list_without_sort',
            'last_companies_list' => 'MetalProductsBundle:Products:companies_list_without_sort',
            'frontpage' => 'MetalProjectBundle:Default:index_subdomain',
            'search' => 'MetalProductsBundle:Products:search',
            'serch_landing' => 'MetalCategoriesBundle:LandingPages:search',
        );


        $currentRoute = $requestBag->get('route_type', 'frontpage');
        $route = isset($routes[$currentRoute]) ? $routes[$currentRoute] : $routes['frontpage'];

        if ($categoryId = $requestBag->get('category')) {
            $category = $em->find('MetalCategoriesBundle:Category', $categoryId);
            /* @var $category Category */
            if ($category) {
                $routes = array_merge(
                    $routes,
                    array(
                        'manufacturers_list' => 'MetalCatalogBundle:Manufacturers:list_category_subdomain',
                        'brands_list' => 'MetalCatalogBundle:Brands:list_category_subdomain',
                        'catalog_products_list' => 'MetalCatalogBundle:Products:list_category_subdomain',
                        'products_list' => 'MetalProductsBundle:Products:list_category_subdomain',
                        'companies_list' => 'MetalProductsBundle:Products:companies_list_category_subdomain',
                    )
                );
                $route = $routes[$requestBag->get('route_type', 'products_list')];

                $criteria['categories_ids'] = $category->getId();

                $filterParameters = $productsHelper->getFilterParametersForCitiesListFromRequest($request);

                if (!empty($filterParameters['attributes_ids'])) {
                    $attributesValue = $em
                        ->getRepository('MetalCategoriesBundle:ParameterOption')
                        ->findBy(array('id' => $filterParameters['attributes_ids']));
                    $attributes = array();
                    foreach ($attributesValue as $attributeValue) {
                        $attributes[$attributeValue->getType()->getId()][] = $attributeValue->getId();
                    }
                    $criteria['attributes_ids'] = $attributes;
                    $itemsType = 'products_count';
                    unset($filterParameters['attributes_ids']);
                }

                if (!empty($filterParameters['cattr'])) {
                    $criteria['company_attributes_ids'] = $filterParameters['cattr'];
                }

                //TODO нужно придумать как в хелпере CounterHelper учитывать промежутки для price_to и price_from
            }
        }


        $territorialHelper = $this->container->get('brouzie.helper_factory')->get('MetalTerritorialBundle');
        $allowCitiesIds = array();
        $allowRegionsIds = array();
        if ($requestBag->get('is_header')) {
            list($allowCitiesIds, $allowRegionsIds) = $territorialHelper->getHeaderCities($itemsType, $criteria, array('city' => 'cities_ids', 'region' => 'regions_ids'));
            $criteria = array('countries_ids' => $country->getId());
        }

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $showAllCitiesCompaniesCount = $em
            ->createQueryBuilder()
            ->select('COUNT(c.id)')
            ->from('MetalCompaniesBundle:Company', 'c')
            ->where('c.visibilityStatus = :fullStatus')
            ->orWhere('c.visibilityStatus = :allCitiesStatus AND c.country = :country')
            ->setParameter('fullStatus', Company::VISIBILITY_STATUS_ALL_COUNTRIES)
            ->setParameter('allCitiesStatus', Company::VISIBILITY_STATUS_ALL_CITIES)
            ->setParameter('country', $country->getId())
            ->getQuery()
            ->getSingleScalarResult();

        if ($showAllCitiesCompaniesCount > 0) {
            $citiesIds =$territorialHelper->getCitiesWithSlug($country);
        } else {
            $counterHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Counter');
            /* @var $counterHelper CounterHelper */

            $citiesIds = $counterHelper->getItemsCountPerObject($itemsType, $criteria, 'cities_ids');
        }

        return JsonResponse::create(
            $apiHelper->getTerritorialStructure(
                $citiesIds,
                $route,
                $country,
                $activeTerritory,
                $category,
                $filterParameters,
                $allowCitiesIds,
                $allowRegionsIds,
                'MetalProjectBundle:Default:index_subdomain'
            )
        );
    }

    public function territorialForLandingPageAction(Request $request, Country $country)
    {
        $requestBag = $request->request;
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $landingId = $requestBag->get('landing_id');

        $activeCity = $request->attributes->get('city');
        $activeRegion = $request->attributes->get('region');
        $activeTerritory = null;
        if ($activeCity) {
            $activeTerritory = $activeCity;
        } elseif ($activeRegion) {
            $activeTerritory = $activeRegion;
        }

        $landingPage = $em->find('MetalCategoriesBundle:LandingPage', $landingId);

        if (!$landingPage) {
            return JsonResponse::create();
        }

        $citiesForLandingPageQb = $em->getRepository('MetalCategoriesBundle:LandingPageCityCount')->createQueryBuilder('lpcc')
            ->select('lpcc')
            ->addSelect('c.id AS cityId')
            ->join('lpcc.city', 'c')
            ->where('lpcc.landingPage = :landing_page')
            ->andWhere('lpcc.resultsCount >= :count')
            ->setParameter('landing_page', $landingPage)
            ->setParameter('count', LandingPage::MIN_PRODUCTS_COUNT)
        ;

        if ($activeCity) {
            $citiesForLandingPageQb
                ->andWhere('lpcc.city <> :_city')
                ->setParameter('_city', $activeCity)
            ;
        }

        $citiesIds = array_column($citiesForLandingPageQb->getQuery()->getResult(), 'cityId', 'cityId');
        $route = 'MetalCategoriesBundle:LandingPage:landing';
        $apiHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Api');
        /* @var $apiHelper ApiHelper */

        $filterParameters['slug'] = $landingPage->getSlug();

        return JsonResponse::create(
            $apiHelper->getTerritorialStructureNew(
                $citiesIds,
                $route,
                $country,
                $activeTerritory,
                $landingPage,
                $filterParameters
            )
        );
    }

    /**
     * @ParamConverter("category", class="MetalCategoriesBundle:Category", options={"id" = "category_id"})
     * @ParamConverter("attribute", class="MetalAttributesBundle:Attribute", options={"id" = "attribute_id"})
     */
    public function getAttributeValuesAction(Request $request, Category $category, Attribute $attribute, $tab)
    {
        $criteria = ProductsFilteringSpec::createFromRequest($request);
        $facetSpec = (new ProductsFacetSpec())
            ->facetByAttribute($attribute, $criteria);

        $dataFetcher = $this->container->get('metal.products.data_fetcher');

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManagerInterface */
        $facetsResultSet = $dataFetcher->getFacetedResultSetByCriteria($criteria, $facetSpec);

        $attributeValueRepo = $em->getRepository('MetalAttributesBundle:AttributeValue');
        $attributesCollection = $attributeValueRepo->loadCollectionByFacetResult(
            new AttributesFacetResult($facetsResultSet),
            array(AttributeValue::ORDER_OUTPUT_PRIORITY)
        );

        $currentAttributesCollection = $attributeValueRepo->loadCollectionByGroups($criteria->productAttrsByGroup);

        $routes = array(
            'products' => 'MetalProductsBundle:Products:list_category_subdomain',
            'companies' => 'MetalProductsBundle:Products:companies_list_category_subdomain',
        );

        return $this->render(
            '@MetalProducts/Products/partial/attribute_values.html.twig',
            array(
                'category' => $category,
                'attribute' => $attribute,
                'attributesCollection' => $attributesCollection,
                'currentAttributesCollection' => $currentAttributesCollection,
                'route' => $routes[$tab],
                'routeParameters' => array('subdomain' => $request->attributes->get('subdomain')),
            )
        );
    }

    /**
     * @ParamConverter("company", class="MetalCompaniesBundle:Company")
     */
    public function getProductImagesAction(Request $request, Company $company)
    {
        $q = $request->query->get('q');

        $productImages = $this->getDoctrine()->getManager()
            ->getRepository('MetalProductsBundle:ProductImage')
            ->createQueryBuilder('productImage')
            ->where('productImage.company = :company')
            ->andWhere('productImage.description LIKE :q')
            ->setParameter('q', '%'.$q.'%')
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult();

        $imageHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Image');
        /* @var $imageHelper ImageHelper */
        $serializedImages = array();
        /* @var $productImages ProductImage[] */
        foreach ($productImages AS $productImage) {
            $serializedImages[] = array(
                'id'    => $productImage->getId(),
                'title' => $productImage->getDescription(),
                'src'   => $imageHelper->getPhotoUrlForProductPhoto($productImage, 'sq250')
            );
        }

        return JsonResponse::create($serializedImages);
    }
}
