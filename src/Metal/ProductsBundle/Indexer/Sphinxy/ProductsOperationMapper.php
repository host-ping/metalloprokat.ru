<?php

namespace Metal\ProductsBundle\Indexer\Sphinxy;

use Brouzie\Bridge\Sphinxy\Indexer\OperationMapper;
use Brouzie\Components\Indexer\Operation\ChangeSet;
use Brouzie\Components\Indexer\Operation\Criteria;
use Brouzie\Sphinxy\QueryBuilder;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\Indexer\Operation\ProductChangeSet;
use Metal\ProductsBundle\Indexer\Operation\ProductsCriteria;

class ProductsOperationMapper implements OperationMapper
{
    public function mapCriteriaToQueryBuilder(Criteria $criteria, QueryBuilder $qb): void
    {
        if (!$criteria instanceof ProductsCriteria) {
            throw new \InvalidArgumentException();
        }

        if (count($criteria->getIds())) {
            $qb->andWhere(sprintf('%s IN %s', ProductIndex::ID, $qb->createParameter($criteria->getIds())));
        }

        if ($criteria->getCompanyId()) {
            $qb->andWhere(
                sprintf('%s = %s', ProductIndex::COMPANY_ID, $qb->createParameter($criteria->getCompanyId()))
            );
        }
    }

    public function mapChangeSetToQueryBuilder(ChangeSet $changeSet, QueryBuilder $qb): void
    {
        if (!$changeSet instanceof ProductChangeSet) {
            throw new \InvalidArgumentException();
        }

        if ($changeSet->isFieldChanged('isSpecialOffer')) {
            $qb->set(ProductIndex::IS_SPECIAL_OFFER, $qb->createParameter($changeSet->getIsSpecialOffer()));
        }

        if ($changeSet->isFieldChanged('isHotOffer')) {
            $qb->set(ProductIndex::IS_HOT_OFFER, $qb->createParameter($changeSet->getIsHotOffer()));
        }

        if ($changeSet->isFieldChanged('updatedAt')) {
            $value = $changeSet->getUpdatedAt()->format('Ymd');
            $qb->set(ProductIndex::DAY_UPDATED_AT, $qb->createParameter($value));
        }

        if ($changeSet->isFieldChanged('companyLastVisitedAt')) {
            $value = $changeSet->getCompanyLastVisitedAt()->getTimestamp();
            $qb->set(ProductIndex::COMPANY_LAST_VISITED_AT, $qb->createParameter($value));
        }

        if ($changeSet->isFieldChanged('companyRating')) {
            $qb->set(ProductIndex::COMPANY_RATING, $qb->createParameter($changeSet->getCompanyRating()));
        }
    }

    //TODO: implement rules providers
    public function getChangeSetRules(): iterable
    {
        yield 'specialOffer' => function (QueryBuilder $qb, bool $isSpecialOffer) {
            $qb->set(ProductIndex::IS_SPECIAL_OFFER, $qb->createParameter($isSpecialOffer));
        };

        yield 'updatedAt' => function (QueryBuilder $qb, \DateTime $updatedAt) {
            $qb->set(ProductIndex::DAY_UPDATED_AT, $qb->createParameter($updatedAt->format('Ymd')));
        };

        yield 'companyRating' => function (QueryBuilder $qb, \DateTime $companyLastVisitedAt) {
            $qb->set(ProductIndex::COMPANY_LAST_VISITED_AT, $qb->createParameter($companyLastVisitedAt->getTimestamp()));
        };

        yield 'companyRating' => function (QueryBuilder $qb, int $companyRating) {
            $qb->set(ProductIndex::COMPANY_RATING, $qb->createParameter($companyRating));
        };
    }
}
