<?php

namespace Metal\ProductsBundle\Widget;

use Brouzie\WidgetsBundle\Cache\CacheKeyGenerator;
use Brouzie\WidgetsBundle\Cache\CacheProfile;
use Brouzie\WidgetsBundle\Widget\CacheableWidget;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;

use Metal\CategoriesBundle\Entity\LandingPage;
use Metal\CategoriesBundle\Helper\ParameterHelper;
use Metal\CategoriesBundle\Entity\CategoryFriends;
use Metal\CategoriesBundle\Repository\CategoryFriendsRepository;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Spec\Aggregation\AttributesIdsAggregation;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFacetSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\AdvancedDataFetcher;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\CountsAggregationResult;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Region;
use Metal\TerritorialBundle\Entity\Country;
use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;

class SeeAlsoCategoriesWidget extends WidgetAbstract implements CacheableWidget
{
    const PER_PAGE = 5;

    public function setDefaultOptions()
    {
        parent::setDefaultOptions();

        //TODO: в перспективе отказаться от products_parameters в пользу attributes_collection
        $this->optionsResolver
            ->setDefined(array('category', 'city', 'region', 'route_name', 'products_parameters'))
            ->setRequired(array('route_name', 'country'))
            ->setAllowedTypes('category', array(Category::class, 'null'))
            ->setAllowedTypes('city', array(City::class, 'null'))
            ->setAllowedTypes('region', array(Region::class, 'null'))
            ->setDefaults(array('page' => 1));
    }

    public function getCacheProfile()
    {
        return new CacheProfile(new CacheKeyGenerator($this), DataFetcher::TTL_1DAY);
    }

    public function getKeys($maxKey, $maxResult = 2)
    {
        $keyGenerator = new CacheKeyGenerator($this);
        $seed = crc32($keyGenerator());

        $result = array();
        mt_srand($seed);
        for ($i = 1; $i <= $maxResult; $i++) {
            $result[] = mt_rand(0, $maxKey);
        }

        return array_unique($result);
    }

    public function getLandingPages()
    {
        $country = $this->options['country'];
        $region = $this->options['region'];
        $city = $this->options['city'];
        $category = $this->options['category'];

        $conn = $this->getDoctrine()->getConnection();
        /* @var $conn Connection */

        $qb = $conn
            ->createQueryBuilder()
            ->select('lp.id')
            ->from('landing_page', 'lp');

        //TODO: несмотря на эти сортировки, этот приоритет теряется после рандома
        if ($category instanceof Category) {
            $qb
                ->addSelect('IF(lp.category_id = :category_id, 1, 0) AS category_match')
                ->addSelect('IF(lp.breadcrumb_category_id = :category_id, 1, 0) AS breadcrumb_category_match')
                ->setParameter('category_id', $category->getId())
                ->addOrderBy('category_match', 'DESC')
                ->addOrderBy('breadcrumb_category_match', 'DESC');
        }

        if ($city instanceof City) {
            $qb
                ->join('lp', 'landing_page_city_count', 'lpcc', 'lp.id = lpcc.landing_page_id and lpcc.city_id = :_city_id')
                ->setParameter('_city_id', $city->getId())
                ->andWhere('lpcc.results_count >= :min_products_count')
                ->setParameter('min_products_count', LandingPage::MIN_PRODUCTS_COUNT);
        }

        if ($region instanceof Region) {
            //FIXME: в рамках данного виджета нам нужно фильтровать а не сортировать, нам не нужны лендинги из других областей
            $qb->addSelect('IF(lp.region_id = :region_id, 1, 0) AS region_match')
                ->setParameter('region_id', $region->getId())
                ->addOrderBy('region_match', 'DESC');
        }

        if ($country instanceof Country) {
            $qb
                ->join('lp', 'landing_page_country_count', 'lpctc', 'lp.id = lpctc.landing_page_id and lpctc.country_id = :_country_id')
                ->setParameter('_country_id', $country->getId())
                ->andWhere('lpctc.results_count >= :min_products_count')
                ->setParameter('min_products_count', LandingPage::MIN_PRODUCTS_COUNT);
        }

        $landingPagesIds = $qb->setMaxResults(50)->execute()->fetchAll();
        $landingPagesIds = array_column($landingPagesIds, 'id');
        $landingPagesIdsToLoad = array();
        if ($landingPagesIds) {
            $keys = $this->getKeys(count($landingPagesIds) - 1);
            foreach ($keys as $key) {
                $landingPagesIdsToLoad[] = $landingPagesIds[$key];
            }

            return $this->getDoctrine()->getRepository('MetalCategoriesBundle:LandingPage')->findBy(array('id' => $landingPagesIdsToLoad));
        }

        return $landingPagesIdsToLoad;
    }

    public function getParametersToRender()
    {
        $category = $this->options['category'];
        /* @var $category Category */
        $country = $this->options['country'];
        /* @var $country Country */
        $page = $this->options['page'];

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $landingPages = $this->getLandingPages();

        $categories = array();
        $results = array();
        $parameters = array();

        $productParams = $this->options['products_parameters'];

        if ($productParams) {
            $res = $this->getParametersObjects($productParams, $page);
            $parameters = $res['parameters'];
            $results = $res['results'];
            $categories = $res['categories'];
        } elseif ($category) {
            $categoryFriendsRepository = $em->getRepository('MetalCategoriesBundle:CategoryFriends');
            /* @var $categoryFriendsRepository CategoryFriendsRepository  */

            $categoryFriends = $categoryFriendsRepository->findOneBy(array('category' => $category->getId()));
            /* @var $categoryFriends CategoryFriends */

            if ($categoryFriends) {
                $results = $categoryFriends->getCategoriesWithFlags();
                $categoriesIds = $this->arraySliceWithCycling(array_keys($results), $page, self::PER_PAGE);
                $categories = $em->getRepository('MetalCategoriesBundle:Category')->findBy(array('id' => $categoriesIds));
            }
        }

        // уменьшаем кол-во результатов по категориям на кол-во результатов по лендингам
        if ($landingPages && $categories) {
            foreach (range(0, count($landingPages) - 1) as $i) {
                unset($categories[$i]);
            }
        }

        $attributesCollection = $this->getRequest()->attributes->get('attributes_collection');

        $dataFetcher = $this->container
            ->get('metal_products.data_fetcher_factory')
            ->getDataFetcher(ProductIndex::SCOPE);

//        $dataFetcher = $this->container->get('metal.products.data_fetcher');

        $specification = ProductsFilteringSpec::createFromRequest($this->getRequest())
            ->attributesCollection($attributesCollection)
        ;

        if ($dataFetcher instanceof AdvancedDataFetcher) {
            $aggregations = [new AttributesIdsAggregation('attributes_ids')];
            $sourceResponse = $dataFetcher->getAggregations($specification, $aggregations, false);

            /** @var CountsAggregationResult $aggregationResult */
            $aggregationResult = $sourceResponse->getAggregationResult('attributes_ids');
            $allAttributes = $aggregationResult->getCounts();
        } else {
            $facetSpec = new ProductsFacetSpec();
            $facetSpec->facetByAttributes();

            $facetsResultSet = $dataFetcher->getFacetedResultSetByCriteria($specification, $facetSpec, false);

            $allAttributes = (new FacetResultExtractor(
                $facetsResultSet, ProductsFacetSpec::COLUMN_ATTRIBUTES_IDS
            ))->getCounts();
        }

        $currentAttributesValuesWithCounts = array_intersect_key($allAttributes, array_flip($attributesCollection->getAttributesValuesIds()));

        $currentAttributeValue = null;
        if ($currentAttributesValuesWithCounts && (count($currentAttributesValuesWithCounts) > 1)) {
            $attributeValueIdWithMaxCount = array_search(max($currentAttributesValuesWithCounts), $currentAttributesValuesWithCounts);
            $currentAttributeValue = $em->find('MetalAttributesBundle:AttributeValue', $attributeValueIdWithMaxCount);
        }

        return array(
            'parameters' => $parameters,
            'categories' => $categories,
            'country' => $country,
            'city' => $this->options['city'],
            'region' => $this->options['region'],
            'results' => $results,
            'landingPages' => $landingPages,
            'currentAttributeValue' => $currentAttributeValue,
        );
    }

    private function arraySliceWithCycling($array, $page, $perPage)
    {
        $n = count($array);
        $offset = ($page - 1) * $perPage;

        return array_slice(array_merge(array_slice($array, $offset % $n), array_slice($array, 0, $offset % $n)), 0, $perPage);
    }

    private function getParametersObjects($productParams, $page)
    {
        $parameterFriendsIds = array();
        $parameterFriendsFlags = array();
        $friendCategoryIds = array();
        $flagsCategory = array();

        switch (count($productParams)) {
            case 1:
                $limit = ParameterHelper::LIMIT_VAL_FOR_1;
                $limitCategory = 2;
                break;
            case 4:
                $limit = ParameterHelper::LIMIT_VAL_FOR_4;
                $limitCategory = 1;
                break;
            case 2:
                $limit = ParameterHelper::LIMIT_VAL_FOR_2;
                $limitCategory = 1;
                break;
            default:
                $limit = 2;
                $limitCategory = 1;
        }

        $maxParametersFriendsIds = ParameterHelper::TOTAL_LIMIT;

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        foreach ($productParams as $productParam) {
            $friendIds = explode(',', $productParam['friendIds']);
            $flags = explode(',', $productParam['friendsFlags']);
            $flags = array_map('intval', $flags);

            foreach ($friendIds as $key => $val) {
                $parameterFriendsIds[] = $val;
                $parameterFriendsFlags[] = $flags[$key];
            }

            $categoryCurrArr = explode(',', $productParam['friendCategoryIds']);
            $flags = explode(',', $productParam['friendsCategoryFlags']);
            $flags = array_map('intval', $flags);

            foreach ($categoryCurrArr as $key => $val) {
                $friendCategoryIds[] = $val;
                $flagsCategory[] = $flags[$key];
            }
        }

        $friendsParametersIds = $this->arraySliceWithCycling($parameterFriendsIds, $page, $limit);

        $parameters = $em->getRepository('MetalCategoriesBundle:Parameter')
            ->createQueryBuilder('p')
            ->addSelect('parameterGroup')
            ->addSelect('o')
            ->join('p.parameterGroup','parameterGroup')
            ->join('p.parameterOption' , 'o')
            ->andWhere('p.id in (:parameter_ids)')
            ->setParameter('parameter_ids', $friendsParametersIds)
            ->getQuery()
            ->getResult();

        $categories = array();
        if (count($parameters) < $maxParametersFriendsIds) {
            $friendsCategoriesIds = $this->arraySliceWithCycling($friendCategoryIds, $page, $limitCategory);
            $categories = $em->getRepository('MetalCategoriesBundle:Category')->findBy(array('id' => $friendsCategoriesIds));
        }

        $results = array_combine($parameterFriendsIds, $parameterFriendsFlags);
        $resultsCategory = array_combine($friendCategoryIds, $flagsCategory);

        foreach ($resultsCategory as $key => $val) {
            $results[$key] = $val;
        }

        return array('results' => $results, 'parameters' => $parameters, 'categories' => $categories);
    }
}
