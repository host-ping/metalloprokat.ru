<?php

namespace Metal\DemandsBundle\DataFetching;

use Brouzie\Sphinxy\Query\ResultSet;
use Brouzie\Sphinxy\QueryBuilder;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\DemandsBundle\DataFetching\Spec\DemandFilteringSpec;
use Metal\DemandsBundle\DataFetching\Spec\DemandOrderingSpec;
use Metal\ProjectBundle\DataFetching\Sphinxy\ConcreteDataFetcher;
use Metal\ProjectBundle\DataFetching\Spec\FilteringSpec;
use Metal\ProjectBundle\DataFetching\Spec\OrderingSpec;
use Metal\ProjectBundle\DataFetching\UnsupportedSpecException;

class DemandsDataFetcher implements ConcreteDataFetcher
{
    public function initializeQueryBuilder(QueryBuilder $qb)
    {
        $qb->select('id')->from('demands');
    }

    public function applyFilteringSpec(QueryBuilder $qb, FilteringSpec $criteria)
    {
        if (!$criteria instanceof DemandFilteringSpec) {
            throw UnsupportedSpecException::create(DemandFilteringSpec::class, $criteria);
        }

        if (null !== $criteria->id) {
            $qb->andWhere('id = :id')
                ->setParameter('id', $criteria->id);
        }

        if ($criteria->ids) {
            $qb->andWhere('id IN :ids')
                ->setParameter('ids', $criteria->ids);
        }

        if (null !== $criteria->noId) {
            $qb->andWhere('id <> :noId')
                ->setParameter('noId', $criteria->noId);
        }

        if (null !== $criteria->matchTitle) {
            $match = AttributeValue::normalizeTitle(trim($criteria->matchTitle));
            $match = $qb->getEscaper()->halfEscapeMatch($match);

            $qb->andWhere('MATCH (:match_title)')
                ->setParameter('match_title', "@title $match");
        }

        if (null !== $criteria->dateFrom) {
            $qb->andWhere('created_at >= :date_from')
                ->setParameter('date_from', $criteria->dateFrom->getTimestamp());
        }

        if (null !== $criteria->dateTo) {
            $qb->andWhere('created_at <= :date_to')
                ->setParameter('date_to', $criteria->dateTo->getTimestamp());
        }

        if (null !== $criteria->concreteCategoryId) {
            $qb->andWhere('category_id = :concrete_category_id')
                ->setParameter('concrete_category_id', $criteria->concreteCategoryId);
        } elseif (null !== $criteria->categoryId) {
            $qb->andWhere('categories_ids = :category_id')
                ->setParameter('category_id', $criteria->categoryId);
        } elseif ($criteria->categoriesIds) {
            $qb->andWhere('categories_ids IN :categories_ids')
                ->setParameter('categories_ids', $criteria->categoriesIds);
        }

        if ($criteria->territorialStructureIds) {
            $qb->andWhere('territorial_structure_ids IN :territorial_structure_ids')
                ->setParameter('territorial_structure_ids', $criteria->territorialStructureIds);
        }

        if (null !== $criteria->cityId) {
            $qb->andWhere('city_id = :city_id')
                ->setParameter('city_id', $criteria->cityId);
        } elseif (null !== $criteria->regionId) {
            $qb->andWhere('region_id = :region_id')
                ->setParameter('region_id', $criteria->regionId);
        } elseif (null !== $criteria->countryId) {
            $qb->andWhere('countries_ids = :country_id')
                ->setParameter('country_id', $criteria->countryId);
        }

        if (null !== $criteria->authorType) {
            $qb->andWhere('author_type = :author_type')
                ->setParameter('author_type', $criteria->authorType);
        }

        if (null !== $criteria->isRepetitive) {
            $qb->andWhere('is_repetitive = :is_repetitive')
                ->setParameter('is_repetitive', $criteria->isRepetitive);
        }

        if (null !== $criteria->isWholesale) {
            $qb->andWhere('is_wholesale = :is_wholesale')
                ->setParameter('is_wholesale', $criteria->isWholesale);
        }

        foreach ($criteria->productAttrsByGroup as $groupId => $values) {
            $qb->andWhere(sprintf('attributes_ids IN %s', $qb->createParameter($values, 'attributes_ids')));
        }
    }

    public function applyOrderingSpec(QueryBuilder $qb, OrderingSpec $orderBy = null)
    {
        if (null === $orderBy) {
            $orderBy = new DemandOrderingSpec();
        } elseif (!$orderBy instanceof DemandOrderingSpec) {
            throw UnsupportedSpecException::create(DemandOrderingSpec::class, $orderBy);
        }

        foreach ($orderBy->getOrders() as $order => $orderAttributes) {
            switch ($order) {
                case 'createdAt':
                    $qb->addOrderBy('created_at', $orderAttributes);
                    break;

                case 'demandViewsCount':
                    $qb->addOrderBy('demand_views_count', $orderAttributes);
                    break;

                case 'random':
                    $qb->addOrderBy('RAND()');
                    break;

                default:
                    throw new \InvalidArgumentException(sprintf('Wrong order "%s"', $order));
            }
        }
    }

    public function filterResultSet(ResultSet $resultSet, FilteringSpec $criteria, $offset, $length)
    {
    }
}
