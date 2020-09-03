<?php

namespace Metal\ProductsBundle\DataFetching\Elastica;

use Elastica\Query;
use Elastica\Query\BoolQuery;
use Elastica\Result;
use Elastica\Script\Script;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\ProductsBundle\DataFetching\DataRequest;
use Metal\ProductsBundle\DataFetching\Result\ProductItem;
use Metal\ProductsBundle\DataFetching\Spec\Aggregation\CountAggregation;
use Metal\ProductsBundle\DataFetching\Spec\Aggregation\ProductsPerCompanyAggregation;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsOrderingSpec;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\DataFetching\Elastica\ConcreteDataFetcher;
use Metal\ProjectBundle\DataFetching\Result\Item;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Region;

class ProductsDataFetcher implements ConcreteDataFetcher
{
    private const ES_SOURCE = [
        'includes' => [
            ProductIndex::TITLE,
            ProductIndex::COMPANY_ID,
            ProductIndex::COMPANY_RATING,
            ProductIndex::COMPANY_ACCESS,
            ProductIndex::COMPANY_CITY_ID,
            ProductIndex::VISIBILITY_STATUS,
            ProductIndex::IS_VIRTUAL,
            ProductIndex::IS_SPECIAL_OFFER,
        ],
    ];

    public function prepareQuery(Query $query, DataRequest $dataRequest, string $queryMode): void
    {
        /** @var ProductsFilteringSpec $filteringSpec */
        $filteringSpec = $dataRequest->getFilteringSpec(ProductsFilteringSpec::class);

        $query->setSource(self::ES_SOURCE);

        if ($filteringSpec->loadCompanies) {
            $query->setParam('collapse', ['field' => ProductIndex::COMPANY_ID]);

//            $qb->addGroupBy('company_id')
//                ->addSelect('COUNT(*) as products_count_by_company')
//                ->addSelect('MIN(price) as company_price')
//                ->addSelect('MAX(day_updated_at) as product_updated_at')
//                ->withinGroupOrderBy('is_virtual', 'ASC');
        }
    }

    public function reflectFilteringSpecToQuery(
        BoolQuery $boolQuery,
        DataRequest $dataRequest,
        string $queryMode
    ): void {
        /** @var ProductsFilteringSpec $filteringSpec */
        $filteringSpec = $dataRequest->getFilteringSpec(ProductsFilteringSpec::class);

        //FIXME: implement morphology
        if (null !== $filteringSpec->matchTitle) {
            $match = AttributeValue::normalizeTitle(trim($filteringSpec->matchTitle));

            if ($filteringSpec->loadCompanies) {
                $boolQuery->addShould(
                    [
                        new Query\Match(ProductIndex::TITLE, $match),
                        new Query\Match(ProductIndex::COMPANY_TITLE, $match),
                    ]
                );
            } else {
                $qq = new Query\Match();
                $qq->setFieldOperator(ProductIndex::TITLE, 'and');
                $qq->setFieldQuery(ProductIndex::TITLE, $match);
                $boolQuery->addMust($qq);
            }
        }

        if (null !== $filteringSpec->showOnPortal) {
            $boolQuery->addFilter(new Query\Term(['show_on_portal' => ['value' => $filteringSpec->showOnPortal]]));
        }

//        if (null !== $spec->notEmptyAttributes) {
//            $qb->andWhere('attributes_ids > 0');
//        }
//
        if (null !== $filteringSpec->companyId) {
            $boolQuery->addFilter(new Query\Term([ProductIndex::COMPANY_ID => ['value' => $filteringSpec->companyId]]));
        }

        if (count($filteringSpec->exceptCompaniesIds)) {
            $boolQuery->addMustNot(new Query\Terms(ProductIndex::COMPANY_ID, $filteringSpec->exceptCompaniesIds));
        }

        if (null !== $filteringSpec->companyCityId) {
            $boolQuery->addFilter(new Query\Term(['company_city_id' => ['value' => $filteringSpec->companyCityId]]));
        }
//
        if (null !== $filteringSpec->productCityId) {
            $boolQuery->addFilter(new Query\Term(['product_city_id' => ['value' => $filteringSpec->productCityId]]));
        } elseif (null !== $filteringSpec->cityId) {
            //TODO: небольшой хак для getItemsCountPerObject, можно будет уалить, когда он будет работать с критерией
            $citiesIds = $filteringSpec->cityId;

            if (!is_array($filteringSpec->cityId)) {
                $citiesIds = [$filteringSpec->cityId];
                if ($filteringSpec->countryId
                    && $virtualCityId = City::getVirtualCityIdForCountry($filteringSpec->countryId)) {
                    $citiesIds[] = City::getESVirtualCityIdForVirtualCityId($virtualCityId);
                } else {
                    //TODO: кидать ошибку?
                }
            }

            $boolQuery->addFilter(new Query\Terms(ProductIndex::CITIES_IDS, $citiesIds));
        } elseif (null !== $filteringSpec->regionId) {
            //TODO: небольшой хак для getItemsCountPerObject, можно будет уалить, когда он будет работать с критерией
            $regionsIds = [$filteringSpec->regionId];

            if (!is_array($regionsIds)) {
                $regionsIds = [$filteringSpec->regionId];
                if ($filteringSpec->countryId
                    && $virtualRegionId = Region::getVirtualRegionIdForCountry($filteringSpec->countryId)) {
                    $regionsIds[] = Region::getESVirtualRegionIdForVirtualRegionId($virtualRegionId);
                } else {
                    //TODO: кидать ошибку?
                }
            }

            $boolQuery->addFilter(new Query\Terms('regions_ids', $regionsIds));
        } elseif (null !== $filteringSpec->countryId) {
            //TODO: небольшой хак для getItemsCountPerObject, можно будет уалить, когда он будет работать с критерией

            if (is_array($filteringSpec->countryId)) {
                $filteringSpec->countryId = reset($filteringSpec->countryId);
            }

            $boolQuery->addFilter(new Query\Terms(ProductIndex::COUNTRIES_IDS, [$filteringSpec->countryId]));
        }

        if (null !== $filteringSpec->concreteCategoryId) {
            $boolQuery->addFilter(new Query\Term(['category_id' => ['value' => $filteringSpec->concreteCategoryId]]));
        } elseif (null !== $filteringSpec->categoryId) {
            $boolQuery->addFilter(new Query\Terms('categories_ids', [$filteringSpec->categoryId]));
        } elseif (null !== $filteringSpec->customCompanyCategoryId) {
            $boolQuery->addFilter(new Query\Terms('custom_categories_ids', [$filteringSpec->customCompanyCategoryId]));
        }

        if (null !== $filteringSpec->price) {
            $priceQuery = new BoolQuery();
            $boolQuery->addFilter($priceQuery);

            if (isset($filteringSpec->price['min']) && isset($filteringSpec->price['max'])) {
                $priceQuery->addShould(
                    [
                        new Query\Term([ProductIndex::PRICE => ['value' => Product::PRICE_CONTRACT]]),
                        new Query\Range(
                            ProductIndex::PRICE,
                            ['gte' => $filteringSpec->price['min'], 'lte' => $filteringSpec->price['max']]
                        ),
                    ]
                );
            } elseif (isset($filteringSpec->price['min'])) {
                $priceQuery->addShould(
                    [
                        new Query\Term([ProductIndex::PRICE => ['value' => Product::PRICE_CONTRACT]]),
                        new Query\Range(ProductIndex::PRICE, ['gte' => $filteringSpec->price['min']]),
                    ]
                );
            } else {
                $priceQuery->addShould(
                    [
                        new Query\Term([ProductIndex::PRICE => ['value' => Product::PRICE_CONTRACT]]),
                        new Query\Range(ProductIndex::PRICE, ['lte' => $filteringSpec->price['max']]),
                    ]
                );
            }
        }

        foreach ($filteringSpec->productAttrsByGroup as $attributeId => $values) {
            $boolQuery->addFilter(new Query\Terms(sprintf('%s.%d', ProductIndex::ATTRIBUTES, $attributeId), $values));
        }

        if ($filteringSpec->priorityShow) {
            $boolQuery->addFilter(new Query\Range(ProductIndex::PRIORITY_SHOW, ['gt' => 0]));

            //FIXME: implement all cases
//            if (isset($spec->priorityShow['city'])) {
//                $qb
//                    ->addSelect('(priority_show <> 0 OR priority_show_territorial.cities.:city_id) AS calculated_priority_show')
//                    ->andWhere('calculated_priority_show = 1')
//                    ->setParameter('city_id', $spec->priorityShow['city']);
//            } elseif (isset($spec->priorityShow['region'])) {
//                $qb
//                    ->addSelect('(priority_show <> 0 OR priority_show_territorial.cities.:region_id) AS calculated_priority_show')
//                    ->andWhere('calculated_priority_show = 1')
//                    ->setParameter('region_id', $spec->priorityShow['region']);
//            } else {
//                $qb->andWhere('priority_show <> 0');
//            }
        }

        if ($filteringSpec->exceptProductsIds) {
            //TODO: проверить, что это действительно работает
            $boolQuery->addMustNot(new Query\Ids(null, $filteringSpec->exceptProductsIds));
        }
//
        if (count($filteringSpec->companyAttrs)) {
            $boolQuery->addFilter(new Query\Terms('company_attributes_ids', $filteringSpec->companyAttrs));
        }
//
        if ($filteringSpec->loadCompanies) {
            $dataRequest->addAggregation(new CountAggregation(self::TOTAL_ITEMS_COUNT_OVERRIDE_AGGREGATION));
        }
//
//        if ($spec->loadPriceRange) {
//            $qb->addSelect('MIN(price) AS min_price')
//                ->addSelect('MAX(price) AS max_price')
//                ->andWhere('price < :max_price')
//                ->setParameter('max_price', Product::PRICE_CONTRACT);
//        }
//
        if (false === $filteringSpec->allowVirtual) {
            $boolQuery->addFilter(new Query\Term(['is_virtual' => ['value' => false]]));
        }
//
//        if ($spec->loadRandomSpecialOfferProductPerCompany) {
//            $qb
//                ->addSelect('company_access')
//                ->addSelect('company_city_id')
//                ->addSelect('is_special_offer')
//                ->addSelect('visibility_status')
//                ->addSelect('DISORDERLY() AS random_product_order')
//                ->addGroupBy('company_id')
//                ->withinGroupOrderBy('is_special_offer', 'DESC')
//                ->addWithinGroupOrderBy('random_product_order');
//        }
//
        if (null !== $filteringSpec->specialOffer) {
            $boolQuery->addFilter(
                new Query\Term([ProductIndex::IS_SPECIAL_OFFER => ['value' => $filteringSpec->specialOffer]])
            );
        }

        if (null !== $filteringSpec->isHotOffer) {
            $boolQuery->addFilter(
                new Query\Term([ProductIndex::IS_HOT_OFFER => ['value' => $filteringSpec->specialOffer]])
            );
        }
    }

    public function reflectOrderingSpecToQuery(Query $query, DataRequest $dataRequest, string $queryMode): void
    {
//        dump(get_class($query));
        /** @var ProductsOrderingSpec $orderingSpec */
        $orderingSpec = $dataRequest->getOrderingSpec(ProductsOrderingSpec::class, new ProductsOrderingSpec());

        /** @var ProductsFilteringSpec $filteringSpec */
        $filteringSpec = $dataRequest->getFilteringSpec(ProductsFilteringSpec::class);

        $searchMode = false;
        if (null !== $filteringSpec->matchTitle) {
            $searchMode = true;
        }

        $orders = $orderingSpec->getOrders();
        foreach ($orders as $order => $orderAttributes) {
            switch ($order) {
                case 'specialOffer':
                    $query->addSort([ProductIndex::IS_SPECIAL_OFFER => ['order' => 'desc']]);
                    break;
//
//                case 'hotOfferPosition':
//                    $qb->addOrderBy('hot_offer_position', 'DESC');
//                    break;
//
//                case 'iterateByCategory':
//                    $qb->addSelect('UNIQUESERIAL(category_id) AS category_id_order')
//                        ->addOrderBy('category_id_order');
//                    break;
//
                case 'updatedAt':
                    $query->addSort([ProductIndex::DAY_UPDATED_AT => ['order' => 'desc']]);
                    break;
//
//                case 'cityId':
//                    $qb->addSelect('IN (cities_ids, :order_city_id) as order_city')
//                        ->addOrderBy('order_city', 'DESC')
//                        ->setParameter('order_city_id', $orderAttributes);
//                    break;
//
//                case 'createdAt':
//                    $qb->addOrderBy('created_at', 'DESC');
//                    break;
//
                case 'payedCompanies':
                    if (isset($orderAttributes['city'])) {
                        $query->addSort([ProductIndex::PRIORITY_SHOW => ['order' => 'desc']]);
                        //TODO: implement
//                        $qb
//                            ->addSelect('(MAX(INTEGER(priority_show_territorial.cities.:city_id), priority_show)) as calculated_priority_show')
//                            ->setParameter('city_id', $orderAttributes['city'])
//                            ->addOrderBy('calculated_priority_show', 'DESC');
                    } elseif (isset($orderAttributes['region'])) {
                        $query->addSort([ProductIndex::PRIORITY_SHOW => ['order' => 'desc']]);
                        //TODO: implement
//                        $qb
//                            ->addSelect('(MAX(INTEGER(priority_show_territorial.regions.:region_id), priority_show)) as calculated_priority_show')
//                            ->setParameter('region_id', $orderAttributes['region'])
//                            ->addOrderBy('calculated_priority_show', 'DESC');
                    } else {
                        $query->addSort([ProductIndex::PRIORITY_SHOW => ['order' => 'desc']]);
                    }

                    break;
//
//                case 'weight':
//                    $qb->setOption('field_weights', '(company_title = 100, product_title = 10)')
//                        ->addOrderBy('WEIGHT()', 'DESC');
//                    break;
//
                case 'position':
                    $query->addSort([ProductIndex::PRODUCT_POSITION => ['order' => 'desc']]);
                    break;

                case 'createdAt':
                    $query->addSort([ProductIndex::PRODUCT_POSITION => ['order' => 'desc']]);
                    break;
//
//                case 'companyCreatedAt':
//                    $qb->addOrderBy('day_company_created_at', 'DESC');
//                    break;
//
                case 'companyLastVisitedAt':
                    $query->addSort([ProductIndex::COMPANY_LAST_VISITED_AT => ['order' => 'desc']]);
                    break;

                case 'iterateByCompany':
                    $query->addSort(
                        [
                            '_script' => [
                                'type' => 'number',
                                'script' => [
                                    'lang' => 'native',
                                    'inline' => 'sequence_ranking',
                                    'params' => [
                                        'field' => ProductIndex::COMPANY_ID,
                                    ],
                                ],
                                'order' => 'ASC',
                            ],
                        ]
                    );

                    if ($queryMode === self::QUERY_MODE_FULL) {
                        next($orders);

                        $innerOrders = [];
                        while ($innerOrder = next($orders)) {
                            $key = key($orders);
                            $innerOrders[$key] = $innerOrder;
                        }

                        if ($innerOrders) {

                            $dataRequest->addAggregation(
                                ProductsPerCompanyAggregation::createFromInnerOrders(
                                    self::ITEMS_OVERRIDE_AGGREGATION,
                                    $innerOrders
                                )
                            );

                        }

                        // stop applying next orders
                        break 2;
                    }
                    break;

                case 'price':
                    $query->addSort([ProductIndex::PRICE => ['order' => 'asc']]);
                    break;

                case 'rating':
                    $query->addSort([ProductIndex::COMPANY_RATING => ['order' => 'desc']]);
                    break;
//
//                case 'title':
//                    $qb->addOrderBy('product_title');
//                    break;
//
                case 'random':
                    $script = new Script('Random r = new Random(); return r.nextInt();');
                    if (null !== $orderAttributes) {
                        $script = new Script(
                            'Random r = new Random(params.seed); return r.nextInt();',
                            ['seed' => $orderAttributes]
                        );
                    }

                    $query->addSort(
                        [
                            '_script' => [
                                    'type' => 'number',
                                    'order' => 'ASC',
                                ] + $script->toArray(),
                        ]
                    );
                    break;
//
//                case 'companyTitle':
//                    $qb->addOrderBy('company_title');
//                    break;
//
//                case 'cityTitle':
//                    $qb->addOrderBy('city_title');
//                    break;
//
                default:
                    throw new \InvalidArgumentException(sprintf('Wrong order "%s"', $order));

////                case 'attributes':
////                    $orderByParts = array();
////                    foreach ($orderAttributes as $val) {
////                        // обычно array-attributes оборачиваются в скобки. А функция IN не работает когда второй аргумент со скобками, поэтому приходится так извращаться
////                        $attributesParameters = array();
////                        foreach ($val as $attr) {
////                            $attributesParameters[] = $qb->createParameter($attr, 'attributes_ids');
////                        }
////                        $orderByParts[] = sprintf('IN (attributes_ids, '.implode(', ', $attributesParameters).')');
////                    }
////
////                    $qb
////                        ->addSelect('('.implode(' AND ', $orderByParts).') AS order_attributes')
////                        ->addOrderBy('order_attributes', 'DESC')
////                    ;
////                    break;
//
            }
        }
    }

    /**
     * @param Result[] $results
     *
     * @return Item[]
     */
    public function createItemsFromResults(array $results, DataRequest $dataRequest): array
    {
        /** @var ProductsFilteringSpec $filteringSpec */
        $filteringSpec = $dataRequest->getFilteringSpec(ProductsFilteringSpec::class);

        $items = [];
        foreach ($results as $result) {
            //FIXME: load products count using separate query, find pages where it needed
            if (!$filteringSpec->loadCompanies) {
                $source = $result->getSource();

                $productItem = new ProductItem(
                    $result->getId(),
                    null,
                    $source[ProductIndex::COMPANY_ACCESS],
                    $source[ProductIndex::COMPANY_CITY_ID],
                    $source[ProductIndex::VISIBILITY_STATUS],
                    $source[ProductIndex::COMPANY_ID]
                );

                $items[] = $productItem;
            } else {
                //FIXME: create company item
            }
        }

        return $items;
    }
}
