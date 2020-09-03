<?php

namespace Metal\CatalogBundle\DataFetching;

use Brouzie\Sphinxy\Connection;
use Brouzie\Sphinxy\Query\ResultSet;
use Brouzie\Sphinxy\QueryBuilder;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductFilteringSpec;
use Metal\CatalogBundle\DataFetching\Spec\CatalogProductOrderingSpec;
use Metal\ProjectBundle\DataFetching\Sphinxy\ConcreteDataFetcher;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\OrderingSpec;
use Metal\ProjectBundle\DataFetching\UnsupportedSpecException;

class CatalogProductsDataFetcher implements ConcreteDataFetcher
{
    private $sphinxy;

    public function __construct(Connection $sphinxy)
    {
        $this->sphinxy = $sphinxy;
    }

    public function initializeQueryBuilder(QueryBuilder $qb)
    {
        $qb
            ->select('id, brand_id, manufacturer_id')
            ->from('catalog_products');
    }

    public function applyFilteringSpec(QueryBuilder $qb, FilteringSpec $criteria)
    {
        if (!$criteria instanceof CatalogProductFilteringSpec) {
            throw UnsupportedSpecException::create(CatalogProductFilteringSpec::class, $criteria);
        }

        if (null !== $criteria->cityId) {
            $qb
                ->andWhere('cities_ids = :city_id')
                ->setParameter('city_id', $criteria->cityId);
        } elseif (null !== $criteria->regionId) {
            $qb
                ->andWhere('regions_ids = :region_id')
                ->setParameter('region_id', $criteria->regionId);
        } elseif (null !== $criteria->countryId) {
            $qb
                ->andWhere('countries_ids = :country_id')
                ->setParameter('country_id', $criteria->countryId);
        }

        if (null !== $criteria->concreteCategoryId) {
            $qb->andWhere('category_id = :concrete_category_id')
                ->setParameter('concrete_category_id', $criteria->concreteCategoryId);
        } elseif (null !== $criteria->categoryId) {
            $qb->andWhere('categories_ids = :category_id')
                ->setParameter('category_id', $criteria->categoryId);
        }

        if ($criteria->loadBrands) {
            $qb->addGroupBy('brand_id')
                ->addSelect('GROUP_CONCAT(id) AS products_ids');
        }

        if ($criteria->loadManufacturers) {
            $qb->addGroupBy('manufacturer_id')
                ->addSelect('GROUP_CONCAT(brand_id) AS brands_ids');
        }


        if ($criteria->match) {
            $match = $qb->getEscaper()->halfEscapeMatch($criteria->match);

            if ($criteria->loadBrands) {
                $qb->andWhere('MATCH (:match_title)')
                    ->setParameter('match_title', "(@brand_title_field $match)");
            } elseif ($criteria->loadManufacturers) {
                $qb->andWhere('MATCH (:match_title)')
                    ->setParameter('match_title', "@manufacturer_title_field $match");
            } else {
                $qb->andWhere('MATCH (:match_title)')
                    ->setParameter(
                        'match_title',
                        "(@title_field $match) | (@brand_title_field $match) | (@manufacturer_title_field $match)"
                    )
                    ->setOption(
                        'field_weights',
                        '(title_field = 100, brand_title_field = 50, manufacturer_title_field = 50)'
                    );
            }
        }

        foreach ($criteria->productAttrsByGroup as $groupId => $value) {
            $qb->andWhere(sprintf('attributes.%s IN %s', $groupId, $qb->createParameter($value, 'attributes')));
        }

        if (null !== $criteria->notBrand) {
            $qb->andWhere('brand_id <> :not_brand_id')
                ->setParameter('not_brand_id', $criteria->notBrand);
        }
    }

    public function applyOrderingSpec(QueryBuilder $qb, OrderingSpec $orderBy = null)
    {
        if (null === $orderBy) {
            $orderBy = new CatalogProductOrderingSpec();
        } elseif (!$orderBy instanceof CatalogProductOrderingSpec) {
            throw UnsupportedSpecException::create(CatalogProductOrderingSpec::class, $orderBy);
        }

        $orders = $orderBy->getOrders();
        foreach ($orders as $order => $orderAttributes) {
            switch ($order) {
                case 'iterateByBrand':
                    $qb
                        ->addSelect('UNIQUESERIAL(brand_id) AS brand_id_order')
                        ->addOrderBy('brand_id_order');
                    break;

                default:
                    throw new \InvalidArgumentException(sprintf('Wrong order "%s"', $order));
            }
        }
    }

    public function filterResultSet(ResultSet $resultSet, FilteringSpec $criteria, $offset, $length)
    {
        /* @var $criteria CatalogProductFilteringSpec */
        if ($criteria->loadProductsCountPerBrand) {
            $this->loadProductsCountPerBrand($resultSet, $criteria);
        }
    }

    protected function loadProductsCountPerBrand(ResultSet $resultSet, FilteringSpec $criteria)
    {
        $brandsIds = array_column(iterator_to_array($resultSet), 'brand_id');

        if (!$brandsIds) {
            return;
        }

        // сбрасываем сортировки, оставляем только фильтры
        $qb = $this->sphinxy->createQueryBuilder();
        $this->initializeQueryBuilder($qb);
        $this->applyFilteringSpec($qb, $criteria);

        $productsPerBrands = $qb
            ->addSelect('COUNT(*) as products_count')
            ->addGroupBy('brand_id')
            ->andWhere('brand_id IN :brand_id')
            ->setParameter('brand_id', $brandsIds)
            ->setFirstResult(0)
            ->getResult();

        $productsPerBrands = array_column(iterator_to_array($productsPerBrands), 'products_count', 'brand_id');

        if (!$productsPerBrands) {
            return;
        }

        foreach ($resultSet as $key => $row) {
            $resultSet[$key]['products_count'] = $productsPerBrands[$row['brand_id']];
        }
    }
}
