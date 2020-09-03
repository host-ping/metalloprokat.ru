<?php

namespace Metal\ProjectBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\DataFetching\Spec\Aggregation\CategoriesAggregation;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\Service\ProductService;
use Metal\ProjectBundle\DataFetching\AdvancedDataFetcher;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\CountsAggregationResult;

class CounterHelper extends HelperAbstract
{
    public function getItemsCount($itemsType, array $criteria)
    {
        return $this->prepareSphinxQb($itemsType, $criteria)
            ->getResult()
            ->getIterator()
            ->current()['_count'];
    }

    /**
     * @param string $itemsType (products_count, demands_count, companies_count)
     * @param array $criteria
     * @param string $objectType столбец по которому группируем, будет ключем массива
     * @param array $orderBy key = column name, volume = order (ASC|DESC)
     * @param int $limit
     *
     * @return array
     */
    public function getItemsCountPerObject($itemsType, array $criteria, $objectType, array $orderBy = array(), $limit = 5000)
    {
        if ('products_count' === $itemsType) {
            //NB! это FC layer.
            //FIXME: добавить поддержку работы с компаниями, может быть что-то еще из фильтров упустили.
            $dataFetcher = $this->container
                ->get('metal_products.data_fetcher_factory')
                ->getDataFetcher(ProductIndex::SCOPE);

            if ($dataFetcher instanceof AdvancedDataFetcher) {
                $filteringSpec = new ProductsFilteringSpec();

                if (!empty($criteria['countries_ids'])) {
                    $filteringSpec->countryId($criteria['countries_ids']);
                }

                if (!empty($criteria['cities_ids'])) {
                    $filteringSpec->cityId($criteria['cities_ids']);
                }

                if (!empty($criteria['regions_ids'])) {
                    $filteringSpec->regionId($criteria['regions_ids']);
                }

                if (array_key_exists('is_virtual', $criteria)) {
                    $filteringSpec->allowVirtual($criteria['is_virtual']);
                }

                $aggregations = [new CategoriesAggregation('categories', $limit)];

                /** @var CountsAggregationResult $categoriesAggResult */
                $categoriesAggResult = $dataFetcher
                    ->getAggregations($filteringSpec, $aggregations, DataFetcher::TTL_1DAY)
                    ->getAggregationResult('categories');

                return $categoriesAggResult->getCounts();
            }
        }

        // эта проверка нужна для того, чтобы не выполнять группировки по $objectType если в критерии по данной колонке пустое множество, иначе будет ошибка
        // проблема возникла когда ввели города дальнего зарубежья http://projects.brouzie.com/browse/MET-2169
        if (isset($criteria[$objectType]) && empty($criteria[$objectType])) {
            return array();
        }

        $cacheKey = sha1(serialize(array($itemsType, $criteria, $objectType, $orderBy, $limit)));
        $cacheItemPool = $this->container->get('cache.app');
        $cacheItem = $cacheItemPool->getItem($cacheKey);

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $sqb = $this->prepareSphinxQb($itemsType, $criteria);

        if (array_key_exists('disorderly_with_seed', $orderBy)) {
            $sqb->addSelect('DISORDERLY(:disorderly_with_seed) AS disorderly_with_seed')
                ->setParameter('disorderly_with_seed', (int)$orderBy['disorderly_with_seed']);

            $orderBy['disorderly_with_seed'] = 'ASC';
        }

        foreach ($orderBy as $column => $order) {
            $sqb->addOrderBy($column, $order);
        }

        $rawResults = $sqb->groupBy($objectType)
            ->addSelect('GROUPBY() _group_by')
            ->setMaxResults($limit)
            ->getResult();

        $results = array();
        foreach ($rawResults as $result) {
            $results[$result['_group_by']] = $result['_count'];
        }

        $cacheItem->set($results);
        $cacheItem->expiresAfter(DataFetcher::TTL_1DAY);

        $cacheItemPool->save($cacheItem);

        return $results;
    }

    protected function prepareSphinxQb($itemsType, array $criteria)
    {
        $indexesConfig = array(
            'products_count' => array(
                'select' => 'COUNT(*)',
                'index' => 'products',
            ),
            'companies_count' => array(
                'select' => 'COUNT(DISTINCT company_id)',
                'index' => 'products',
            ),
            'demands_count' => array(
                'select' => 'COUNT(*)',
                'index' => 'demands',
            ),
            'catalog_products_count' => array(
                'select' => 'COUNT(*)',
                'index' => 'catalog_products',
            ),
            'manufacturers_count' => array(
                'select' => 'COUNT(DISTINCT manufacturer_id)',
                'index' => 'catalog_products',
            ),
            'brands_count' => array(
                'select' => 'COUNT(DISTINCT brand_id)',
                'index' => 'catalog_products',
            ),
        );

        $qb = $this->container->get('sphinxy.default_connection')
            ->createQueryBuilder()
            ->select($indexesConfig[$itemsType]['select'].' AS _count')
            ->from($indexesConfig[$itemsType]['index']);

        ProductService::applySimpleCriteriaToQueryBuilder($qb, $criteria);

        return $qb;
    }
}
