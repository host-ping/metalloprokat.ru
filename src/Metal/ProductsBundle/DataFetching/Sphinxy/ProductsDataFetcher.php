<?php

namespace Metal\ProductsBundle\DataFetching\Sphinxy;

use Brouzie\Sphinxy\Connection;
use Brouzie\Sphinxy\Query\ResultSet;
use Brouzie\Sphinxy\QueryBuilder;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\DataFetching\Result\ProductItem;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProductsBundle\DataFetching\Spec\ProductsOrderingSpec;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\DataFetching\Sphinxy\ConcreteDataFetcher;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\OrderingSpec;
use Metal\ProjectBundle\DataFetching\UnsupportedSpecException;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Region;

class ProductsDataFetcher implements ConcreteDataFetcher
{
    private $sphinxy;

    public function __construct(Connection $sphinxy)
    {
        $this->sphinxy = $sphinxy;
    }

    public function initializeQueryBuilder(QueryBuilder $qb)
    {
        $qb
            ->select('id')
            ->addSelect('company_id')
            ->addSelect('company_title')
            ->addSelect('product_title')
            ->addSelect('company_access')
            ->addSelect('company_city_id')
            ->addSelect('visibility_status')
            ->addSelect('id AS product_id')
            ->from('products');
    }

    public function applyFilteringSpec(QueryBuilder $qb, FilteringSpec $criteria)
    {
        if (!$criteria instanceof ProductsFilteringSpec) {
            throw UnsupportedSpecException::create(ProductsFilteringSpec::class, $criteria);
        }

        if (null !== $criteria->matchTitle) {
            $match = AttributeValue::normalizeTitle(trim($criteria->matchTitle));
            $match = $qb->getEscaper()->halfEscapeMatch($match);

            if ($criteria->loadCompanies) {
                $qb->andWhere('MATCH (:match_title)')
                    ->setParameter('match_title', "(@company_title_field $match) | (@title $match)");
            } else {
                $qb->andWhere('MATCH (:match_title)')
                    ->setParameter('match_title', "@title $match");
            }
        }

        if (null !== $criteria->showOnPortal) {
            $qb->andWhere('show_on_portal = :show_on_portal')
                ->setParameter('show_on_portal', $criteria->showOnPortal);
        }

        if (null !== $criteria->notEmptyAttributes) {
            $qb->andWhere('attributes_ids > 0');
        }

        if (null !== $criteria->companyId) {
            $qb->andWhere('company_id = :company_id')
                ->setParameter('company_id', $criteria->companyId);
        }

        if ($criteria->exceptCompaniesIds) {
            $qb->andWhere('company_id NOT IN :ex_company_id')
                ->setParameter('ex_company_id', $criteria->exceptCompaniesIds);
        }

        if (null !== $criteria->companyCityId) {
            $qb->andWhere('company_city_id = :company_city_id')
                ->setParameter('company_city_id', $criteria->companyCityId);
        }

        if (null !== $criteria->productCityId) {
            $qb->andWhere('product_city_id = :product_city_id')
                ->setParameter('product_city_id', $criteria->productCityId);
        } elseif (null !== $criteria->cityId) {
            if ($criteria->countryId && $virtualCityId = City::getVirtualCityIdForCountry($criteria->countryId)) {
                $qb->andWhere('cities_ids IN (:city_id, :virtual_city_id)')
                    ->setParameter('virtual_city_id', $virtualCityId)
                    ->setParameter('city_id', $criteria->cityId);
            } else {
                //TODO: кидать ошибку?
                $qb->andWhere('cities_ids  = :city_id')
                    ->setParameter('city_id', $criteria->cityId);
            }
        } elseif (null !== $criteria->regionId) {
            if ($criteria->countryId && $virtualRegionId = Region::getVirtualRegionIdForCountry($criteria->countryId)) {
                $qb->andWhere('regions_ids IN (:region_id, :virtual_region_id)')
                    ->setParameter('virtual_region_id', $virtualRegionId)
                    ->setParameter('region_id', $criteria->regionId);
            } else {
                //TODO: кидать ошибку?
                $qb->andWhere('regions_ids  = :region_id')
                    ->setParameter('region_id', $criteria->regionId);
            }
        } elseif (null !== $criteria->countryId) {
            $qb->andWhere('countries_ids = :country_id')
                ->setParameter('country_id', $criteria->countryId);
        }

        if (null !== $criteria->concreteCategoryId) {
            $qb->andWhere('category_id = :concrete_category_id')
                ->setParameter('concrete_category_id', $criteria->concreteCategoryId);
        } elseif (null !== $criteria->categoryId) {
            $qb->andWhere('categories_ids = :category_id')
                ->setParameter('category_id', $criteria->categoryId);
        } elseif (null !== $criteria->customCompanyCategoryId) {
            $qb->andWhere('custom_categories_ids = :custom_category_id')
                ->setParameter('custom_category_id', $criteria->customCompanyCategoryId);
        }

        if (null !== $criteria->price) {
            if (isset($criteria->price['min'], $criteria->price['max'])) {
                $qb->addSelect(
                    '(price = :price_contract OR (price <= :price_max AND price >= :price_min)) AS price_condition'
                )
                    ->setParameter('price_min', $criteria->price['min'] * 1.0)
                    ->setParameter('price_max', $criteria->price['max'] * 1.0);
            } elseif (isset($criteria->price['min'])) {
                $qb->addSelect('(price = :price_contract OR price >= :price_min) AS price_condition')
                    ->setParameter('price_min', $criteria->price['min'] * 1.0);
            } else {
                $qb->addSelect('(price = :price_contract OR price <= :price_max) AS price_condition')
                    ->setParameter('price_max', $criteria->price['max'] * 1.0);
            }

            $qb
                ->andWhere('price_condition = 1')
                ->setParameter('price_contract', Product::PRICE_CONTRACT);
        }

        if (null !== $criteria->productAttrsByGroup) {
            foreach ($criteria->productAttrsByGroup as $groupId => $values) {
                $qb->andWhere(sprintf('attributes_ids IN %s', $qb->createParameter($values, 'attributes_ids')));
            }
        }

        if ($criteria->priorityShow) {
            if (isset($criteria->priorityShow['city'])) {
                $qb
                    ->addSelect(
                        '(priority_show <> 0 OR priority_show_territorial.cities.:city_id) AS calculated_priority_show'
                    )
                    ->andWhere('calculated_priority_show = 1')
                    ->setParameter('city_id', $criteria->priorityShow['city']);
            } elseif (isset($criteria->priorityShow['region'])) {
                $qb
                    ->addSelect(
                        '(priority_show <> 0 OR priority_show_territorial.cities.:region_id) AS calculated_priority_show'
                    )
                    ->andWhere('calculated_priority_show = 1')
                    ->setParameter('region_id', $criteria->priorityShow['region']);
            } else {
                $qb->andWhere('priority_show <> 0');
            }
        }

        if ($criteria->exceptProductsIds) {
            $qb->andWhere('id NOT IN :excluded_ids')
                ->setParameter('excluded_ids', $criteria->exceptProductsIds);
        }

        if (count($criteria->companyAttrs)) {
            $qb->andWhere('company_attributes_ids IN :company_attributes_ids')
                ->setParameter('company_attributes_ids', $criteria->companyAttrs);
        }

        if ($criteria->loadCompanies) {
            $qb->addGroupBy('company_id')
                ->addSelect('COUNT(*) as products_count_by_company')
                ->addSelect('MIN(price) as company_price')
                ->addSelect('MAX(day_updated_at) as product_updated_at')
                ->withinGroupOrderBy('is_virtual', 'ASC');
        }

        if ($criteria->loadPriceRange) {
            $qb->addSelect('MIN(price) AS min_price')
                ->addSelect('MAX(price) AS max_price')
                ->andWhere('price < :max_price')
                ->setParameter('max_price', Product::PRICE_CONTRACT);
        }

        if (!$criteria->allowVirtual) {
            $qb->andWhere('is_virtual = 0');
        }

        if ($criteria->loadRandomSpecialOfferProductPerCompany) {
            $qb
                ->addSelect('is_special_offer')
                ->addSelect('DISORDERLY() AS random_product_order')
                ->addGroupBy('company_id')
                ->withinGroupOrderBy('is_special_offer', 'DESC')
                ->addWithinGroupOrderBy('random_product_order');
        }

        if ($criteria->specialOffer) {
            $qb->andWhere('is_special_offer = 1');
        }

        if ($criteria->isHotOffer) {
            $qb->andWhere('is_hot_offer = 1');
        }
    }

    public function applyOrderingSpec(QueryBuilder $qb, OrderingSpec $orderBy = null)
    {
        if (null === $orderBy) {
            $orderBy = new ProductsOrderingSpec();
        } elseif (!$orderBy instanceof ProductsOrderingSpec) {
            throw UnsupportedSpecException::create(ProductsOrderingSpec::class, $orderBy);
        }

        $orders = $orderBy->getOrders();
        foreach ($orders as $order => $orderAttributes) {
            switch ($order) {

                case 'specialOffer':
                    $qb->addOrderBy('is_special_offer', 'DESC');
                    break;

                case 'hotOfferPosition':
                    $qb->addOrderBy('hot_offer_position', 'DESC');
                    break;

                case 'iterateByCategory':
                    $qb->addSelect('UNIQUESERIAL(category_id) AS category_id_order')
                        ->addOrderBy('category_id_order');
                    break;

                case 'updatedAt':
                    $qb->addOrderBy('day_updated_at', 'DESC');
                    break;

                case 'cityId':
                    $qb->addSelect('IN (cities_ids, :order_city_id) as order_city')
                        ->addOrderBy('order_city', 'DESC')
                        ->setParameter('order_city_id', $orderAttributes);
                    break;

                case 'createdAt':
                    $qb->addOrderBy('created_at', 'DESC');
                    break;

                case 'payedCompanies':
                    if (isset($orderAttributes['city'])) {
                        $qb
                            ->addSelect(
                                '(MAX(INTEGER(priority_show_territorial.cities.:city_id), priority_show)) as calculated_priority_show'
                            )
                            ->setParameter('city_id', $orderAttributes['city'])
                            ->addOrderBy('calculated_priority_show', 'DESC');
                    } elseif (isset($orderAttributes['region'])) {
                        $qb
                            ->addSelect(
                                '(MAX(INTEGER(priority_show_territorial.regions.:region_id), priority_show)) as calculated_priority_show'
                            )
                            ->setParameter('region_id', $orderAttributes['region'])
                            ->addOrderBy('calculated_priority_show', 'DESC');
                    } else {
                        $qb->addOrderBy('priority_show', 'DESC');
                    }

                    break;

                case 'weight':
                    $qb->setOption('field_weights', '(company_title = 100, product_title = 10)')
                        ->addOrderBy('WEIGHT()', 'DESC');
                    break;

                case 'position':
                    $qb->addOrderBy('product_position', 'ASC');
                    break;

                case 'companyCreatedAt':
                    $qb->addOrderBy('day_company_created_at', 'DESC');
                    break;

                case 'companyLastVisitedAt':
                    $qb->addOrderBy('company_last_visited_at', 'DESC');
                    break;

                case 'iterateByCompany':
                    $qb->addSelect('UNIQUESERIAL(company_id) AS company_id_order')
                        ->addOrderBy('company_id_order');
                    break;

                case 'price':
                    $qb->addOrderBy('price');
                    break;

                case 'rating':
                    $qb->addOrderBy('company_rating', 'DESC');
                    break;

                case 'title':
                    $qb->addOrderBy('product_title');
                    break;

                case 'random':
                    if ($orderAttributes) {
                        $qb->addSelect('DISORDERLY(:random_seed) random_order')
                            ->setParameter('random_seed', $orderAttributes);
                    } else {
                        $qb->addSelect('DISORDERLY() AS random_order');
                    }
                    $qb->addOrderBy('random_order');
                    break;

                case 'companyTitle':
                    $qb->addOrderBy('company_title');
                    break;

                case 'cityTitle':
                    $qb->addOrderBy('city_title');
                    break;

                default:
                    throw new \InvalidArgumentException(sprintf('Wrong order "%s"', $order));

//                case 'attributes':
//                    $orderByParts = array();
//                    foreach ($orderAttributes as $val) {
//                        // обычно array-attributes оборачиваются в скобки. А функция IN не работает когда второй аргумент со скобками, поэтому приходится так извращаться
//                        $attributesParameters = array();
//                        foreach ($val as $attr) {
//                            $attributesParameters[] = $qb->createParameter($attr, 'attributes_ids');
//                        }
//                        $orderByParts[] = sprintf('IN (attributes_ids, '.implode(', ', $attributesParameters).')');
//                    }
//
//                    $qb
//                        ->addSelect('('.implode(' AND ', $orderByParts).') AS order_attributes')
//                        ->addOrderBy('order_attributes', 'DESC')
//                    ;
//                    break;

            }
        }
    }

    public function filterResultSet(ResultSet $resultSet, FilteringSpec $criteria, $offset, $length)
    {
        /* @var $criteria ProductsFilteringSpec */

        if ($criteria->loadProductsCountPerCompany) {
            $this->loadProductsCountPerCompany($resultSet, $criteria);
        }

        if ($criteria->loadCompanies && null === $criteria->productAttrsByGroup && 1 !== $length) {
            // вычитаем виртуальный товар, который есть в каждой компании если он попал в выборку
            $this->fixProductsCountByCompany($resultSet, $criteria);
        }

        if ($criteria->loadSpecialOfferProduct) {
            // для первого вывода платной компании нужно подменить ее продукт на какой-то из СП
            $this->replaceFirstProductWithSpecialOffer($resultSet, $criteria, $offset);
        }

        foreach ($resultSet as $key => $row) {
            if (!$criteria->loadCompanies) {
                $item = new ProductItem(
                    $row['id'], 
                    $row['products_count'] ?? null,
                    $row['company_access'],
                    $row['company_city_id'],
                    $row['visibility_status'],
                    $row['company_id']
                );
                $resultSet[$key]['_item'] = $item;
            } else {
                //FIXME: implement
            }
        }
    }

    protected function loadProductsCountPerCompany(ResultSet $resultSet, FilteringSpec $criteria)
    {
        $companiesIds = array_column(iterator_to_array($resultSet), 'company_id');

        if (!$companiesIds) {
            return;
        }

        // сбрасываем сортировки, оставляем только фильтры
        $qb = $this->sphinxy->createQueryBuilder();
        $this->initializeQueryBuilder($qb);
        $this->applyFilteringSpec($qb, $criteria);

        $productsPerCompanies = $qb
            ->addSelect('COUNT(*) as products_count')
            ->addGroupBy('company_id')
            ->andWhere('company_id IN :companies_ids')
            ->andWhere('is_virtual = 0')
            ->setParameter('companies_ids', $companiesIds)
            ->setFirstResult(0)
            ->setOption('max_matches', 500)
            ->setMaxResults(500)
            ->getResult();

        $productsPerCompanies = array_column(
            iterator_to_array($productsPerCompanies),
            'products_count',
            'company_id'
        );

        if (!$productsPerCompanies) {
            return;
        }

        foreach ($resultSet as $key => $row) {
            $resultSet[$key]['products_count'] = $productsPerCompanies[$row['company_id']];
        }
    }

    protected function fixProductsCountByCompany(ResultSet $resultSet, FilteringSpec $criteria)
    {
        $companiesIds = array_column(iterator_to_array($resultSet), 'company_id');

        if (!$companiesIds) {
            return;
        }

        // сбрасываем сортировки, оставляем только фильтры
        $qb = $this->sphinxy->createQueryBuilder();
        $this->initializeQueryBuilder($qb);
        $this->applyFilteringSpec($qb, $criteria);

        $companiesResult = $qb
            ->andWhere('company_id IN :companies_ids')
            ->andWhere('is_virtual = 1')
            ->setParameter('companies_ids', $companiesIds)
            ->setFirstResult(0)
            ->setOption('max_matches', 500)
            ->setMaxResults(500)
            ->getResult();

        $foundCompaniesIds = array_column(
            iterator_to_array($companiesResult),
            'company_id',
            'company_id'
        );

        foreach ($resultSet as $key => $row) {
            if (array_key_exists((int)$row['company_id'], $foundCompaniesIds)) {
                $resultSet[$key]['products_count_by_company']--;
            }
        }
    }

    protected function replaceFirstProductWithSpecialOffer(ResultSet $resultSet, FilteringSpec $criteria, $offset)
    {
        // сбрасываем сортировки, оставляем только фильтры
        $qb = $this->sphinxy->createQueryBuilder();
        $this->initializeQueryBuilder($qb);
        $this->applyFilteringSpec($qb, $criteria);

        $bestProductsPerCompanies = $qb
            ->addSelect('id AS best_product_id')
            ->addGroupBy('company_id')
            ->withinGroupOrderBy('is_special_offer', 'DESC')
            ->andWhere('priority_show = 2')
            ->setFirstResult(0)
            ->setMaxResults(1000)
            ->getResult();

        $bestProductsPerCompanies = array_column(
            iterator_to_array($bestProductsPerCompanies),
            'best_product_id',
            'company_id'
        );

        // нужно подменить N продуктов: кол-во платников - кол-во уже показанных продуктов
        $companiesCountToReplaceProducts = count($bestProductsPerCompanies) - $offset;

        foreach ($resultSet as $key => $row) {
            if ($companiesCountToReplaceProducts <= 0) {
                break;
            }

            if (isset($bestProductsPerCompanies[$row['company_id']])) {
                $resultSet[$key]['id'] = $bestProductsPerCompanies[$row['company_id']];
                $resultSet[$key]['product_id'] = $bestProductsPerCompanies[$row['company_id']];
            }
            $companiesCountToReplaceProducts--;
        }
    }
}
