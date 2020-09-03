<?php

namespace Metal\DemandsBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Metal\AttributesBundle\DataFetching\AttributesFacetResult;
use Metal\AttributesBundle\Entity\Attribute;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\CategoriesBundle\Entity\Category;
use Metal\DemandsBundle\DataFetching\Spec\DemandFacetSpec;
use Metal\DemandsBundle\DataFetching\Spec\DemandFilteringSpec;
use Doctrine\ORM\EntityManager;
use Metal\DemandsBundle\Helper\DefaultHelper;
use Metal\ProjectBundle\Helper\ApiHelper;
use Metal\ProjectBundle\Helper\CounterHelper;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Helper\DefaultHelper as TerritorialHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ApiController extends Controller
{
    /**
     * @ParamConverter("category", class="MetalCategoriesBundle:Category", options={"id" = "category_id"})
     */
    public function getDemandsCountAction(Request $request, Category $category)
    {
        $criteria = DemandFilteringSpec::createFromRequest($request);
        $demandsDataFetcher = $this->get('metal.demands.data_fetcher');

        $count = $demandsDataFetcher->getItemsCountByCriteria($criteria);

        $attributesCollection = $this->getDoctrine()->getRepository('MetalAttributesBundle:AttributeValue')
            ->loadCollectionByGroups($criteria->productAttrsByGroup);

        $queryBag = $request->query;

        $routeParams = array('category_slug' => $category->getUrl($attributesCollection->getUrl()));

        $routeParams['periodicity'] = $queryBag->get('periodicity');
        $routeParams['consumers'] = $queryBag->get('consumers');
        $routeParams['wholesale'] = $queryBag->get('wholesale');

        $requestRouteParams = $request->attributes->get('_route_params');
        unset($requestRouteParams['category_id']);
        $routeParams = array_merge($routeParams, $requestRouteParams);

        $routeParams = array_filter($routeParams);

        return JsonResponse::create(
            array(
                'count' => $count,
                'countFormatted' => $this->get('sonata.intl.templating.helper.number')->formatDecimal($count),
                'url' => $this->generateUrl('MetalDemandsBundle:Demands:list_subdomain_category', $routeParams)
            )
        );
    }

    public function territorialAction(Request $request, Country $country)
    {
        $queryBag = $request->request;
        $categoryId = $queryBag->get('category');
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $demandHelper = $this->get('brouzie.helper_factory')->get('MetalDemandsBundle');
        /* @var $demandHelper DefaultHelper */
        $territorialHelper = $this->container->get('brouzie.helper_factory')->get('MetalTerritorialBundle');
        /* @var $territorialHelper TerritorialHelper */

        $itemsType = $queryBag->get('counter_name');

        $filterParameters = array();

        $activeCity = $request->attributes->get('city');
        $activeRegion = $request->attributes->get('region');
        $activeTerritory = null;
        if ($activeCity) {
            $activeTerritory = $activeCity;
        } elseif ($activeRegion) {
            $activeTerritory = $activeRegion;
        }

        $criteria = array();
        $criteria['country_id'] = $country->getId();
        $category = null;

        $routes = array(
            'demands_list' => 'MetalDemandsBundle:Demands:list_subdomain',
            'frontpage' => 'MetalProjectBundle:Default:index_subdomain',
            'search' => 'MetalDemandsBundle:Demands:search',
        );

        $route = $routes[$queryBag->get('route_type', 'frontpage')];


        if ($categoryId) {
            $category = $em->find('MetalCategoriesBundle:Category', $categoryId);
            if ($category) {
                $route = 'MetalDemandsBundle:Demands:list_subdomain_category';
                $criteria['categories_ids'] = $category->getId();
                $filterParameters = $demandHelper->getFilterParametersForCitiesList($request);

                if ($filterParameters) {
                    $request = $request->duplicate($filterParameters);
                    list($criteriaForRequest) = $demandHelper->prepareCriteriaForRequest($request);
                    $criteria = array_merge($criteria, $criteriaForRequest);

                    //TODO: убрать это непотребство
                    unset($criteria['date_to'], $criteria['city_id']);
                }
            }
        }

        $allowCitiesIds = array();
        $allowRegionsIds = array();
        if ($queryBag->get('is_header')) {
            list($allowCitiesIds, $allowRegionsIds) = $territorialHelper->getHeaderCities($itemsType, $criteria, array('city' => 'city_id', 'region' => 'region_id'));
            $criteria = array('country_id' => $country->getId());
        }

        $counterHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Counter');
        /* @var $counterHelper CounterHelper */

        $citiesIds = $counterHelper->getItemsCountPerObject($itemsType, $criteria, 'city_id');

        $apiHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Api');
        /* @var $apiHelper ApiHelper */

        #FIXME: delete-after-merge-facets
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
                'MetalDemandsBundle:Default:index_subdomain'
            )
        );
    }

    /**
     * @ParamConverter("category", class="MetalCategoriesBundle:Category", options={"id" = "category_id"})
     * @ParamConverter("attribute", class="MetalAttributesBundle:Attribute", options={"id" = "attribute_id"})
     */
    public function getAttributeValuesAction(Request $request, Category $category, Attribute $attribute)
    {
        $criteria = DemandFilteringSpec::createFromRequest($request);
        $facetSpec = (new DemandFacetSpec())
            ->facetByAttribute($attribute, $category, $criteria);

        $dataFetcher = $this->container->get('metal.demands.data_fetcher');

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManagerInterface */
        $facetsResultSet = $dataFetcher->getFacetedResultSetByCriteria($criteria, $facetSpec);

        $attributeValueRepo = $em->getRepository('MetalAttributesBundle:AttributeValue');
        $attributesCollection = $attributeValueRepo->loadCollectionByFacetResult(
            new AttributesFacetResult($facetsResultSet),
            array(AttributeValue::ORDER_OUTPUT_PRIORITY)
        );

        $currentAttributesCollection = $attributeValueRepo->loadCollectionByGroups($criteria->productAttrsByGroup);

        $route = 'MetalDemandsBundle:Demands:list_subdomain_category';

        return $this->render(
            '@MetalProducts/Products/partial/attribute_values.html.twig',
            array(
                'category' => $category,
                'attribute' => $attribute,
                'attributesCollection' => $attributesCollection,
                'currentAttributesCollection' => $currentAttributesCollection,
                'route' => $route,
                'routeParameters' => array('subdomain' => $request->attributes->get('subdomain')),
            )
        );
    }
}
