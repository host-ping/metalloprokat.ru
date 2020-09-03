<?php

namespace Metal\CatalogBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Metal\AttributesBundle\DataFetching\AttributesFacetResult;
use Metal\AttributesBundle\Entity\Attribute;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFacetSpec;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFilteringSpec;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\ProjectBundle\Helper\ApiHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    /**
     * @ParamConverter("category", class="MetalCategoriesBundle:Category", options={"id" = "category_id"})
     * @ParamConverter("attribute", class="MetalAttributesBundle:Attribute", options={"id" = "attribute_id"})
     */
    public function getAttributeValuesAction(Request $request, Category $category, Attribute $attribute, $tab)
    {
        $criteria = CatalogProductFilteringSpec::createFromRequest($request);
        $facetSpec = (new CatalogProductFacetSpec())
            ->facetByAttribute($attribute, $criteria);

        $dataFetcher = $this->container->get('metal.catalog.data_fetcher');
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
            'catalog_products' => 'MetalCatalogBundle:Products:list_category_subdomain',
            'brands' => 'MetalCatalogBundle:Brands:list_category_subdomain',
            'manufacturers' => 'MetalCatalogBundle:Manufacturers:list_category_subdomain',
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
     * @ParamConverter("category", class="MetalCategoriesBundle:Category", options={"id" = "category_id"})
     */
    public function territorialAction(Request $request, $route_type, Category $category, $tab)
    {
        //FIXME: ели передается is_header то выгружаем все города, но в которых нету товаров/компаний/заявок генерим ссылку на главную
        $activeTerritory = null;
        if ($activeCity = $request->attributes->get('city')) {
            $activeTerritory = $activeCity;
        } elseif ($activeRegion = $request->attributes->get('region')) {
            $activeTerritory = $activeRegion;
        }

        $routes = array(
            'manufacturers_list' => 'MetalCatalogBundle:Manufacturers:list_category_subdomain',
            'brands_list' => 'MetalCatalogBundle:Brands:list_category_subdomain',
            'catalog_products_list' => 'MetalCatalogBundle:Products:list_category_subdomain',
        );

        $dataFetcher = $this->container->get('metal.catalog.data_fetcher');
        $specification = CatalogProductFilteringSpec::createFromRequest($request);

        $facetSpec = new CatalogProductFacetSpec();
        $facetSpec->facetByCities($specification);
        $facetsResultSet = $dataFetcher->getFacetedResultSetByCriteria($specification, $facetSpec);
        $facetResultExtractor = new FacetResultExtractor($facetsResultSet, $facetSpec::COLUMN_CITIES_IDS);
        $citiesIds = $facetResultExtractor->getIds();

        $attributesCollection = $this->getDoctrine()->getRepository('MetalAttributesBundle:AttributeValue')
            ->loadCollectionByGroups($specification->productAttrsByGroup);

        $routeParams = array('category_slug' => $category->getUrl($attributesCollection->getUrl()));
        $requestRouteParams = $request->attributes->get('_route_params');
        unset($requestRouteParams['category_id'], $requestRouteParams['route_type']);
        $routeParams = array_filter(array_merge($routeParams, $requestRouteParams));

        $apiHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Api');
        /* @var $apiHelper ApiHelper */

        #FIXME: delete-after-merge-facets
        return JsonResponse::create(
            $apiHelper->getTerritorialStructureNew(
                $citiesIds,
                $routes[$route_type],
                $request->attributes->get('country'),
                $activeTerritory,
                $category,
                $routeParams
            )
        );
    }

    /**
     * @ParamConverter("category", class="MetalCategoriesBundle:Category", options={"id" = "category_id"})
     */
    public function getItemsCountAction(Request $request, Category $category, $tab)
    {
        $criteria = CatalogProductFilteringSpec::createFromRequest($request);
        switch ($tab) {
            case 'brands':
                $route = 'MetalCatalogBundle:Brands:list_category_subdomain';
                $criteria->loadBrands(true);
                break;

            case 'manufacturers':
                $route = 'MetalCatalogBundle:Manufacturers:list_category_subdomain';
                $criteria->loadManufacturers(true);
                break;

            default :
                $route = 'MetalCatalogBundle:Products:list_category_subdomain';
        }

        $count = $this->get('metal.catalog.data_fetcher')->getItemsCountByCriteria($criteria);

        $attributesCollection = $this->getDoctrine()->getRepository('MetalAttributesBundle:AttributeValue')
            ->loadCollectionByGroups($criteria->productAttrsByGroup);

        return JsonResponse::create(
            array(
                'count' => $count,
                'countFormatted' => $this->get('sonata.intl.templating.helper.number')->formatDecimal($count),
                'url' => $this->generateUrl(
                    $route,
                    array(
                        'subdomain' => $request->attributes->get('subdomain'),
                        'category_slug' => $category->getUrl($attributesCollection->getUrl())
                    )
                )
            )
        );
    }
}
