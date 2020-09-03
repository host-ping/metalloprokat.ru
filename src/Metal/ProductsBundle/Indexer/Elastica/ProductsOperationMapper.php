<?php

namespace Metal\ProductsBundle\Indexer\Elastica;

use Brouzie\Components\Indexer\Elastica\OperationMapper;
use Brouzie\Components\Indexer\Operation\ChangeSet;
use Brouzie\Components\Indexer\Operation\Criteria;
use Elastica\Query;
use Metal\ProductsBundle\DataFetching\Elastica\ProductIndex;
use Metal\ProductsBundle\Indexer\Operation\ProductChangeSet;
use Metal\ProductsBundle\Indexer\Operation\ProductsCriteria;

class ProductsOperationMapper implements OperationMapper
{
    public function mapCriteriaToQuery(Query\BoolQuery $query, Criteria $criteria): void
    {
        if (!$criteria instanceof ProductsCriteria) {
            throw new \InvalidArgumentException();
        }

        if (count($criteria->getIds())) {
            $query->addFilter(
                new Query\Terms(ProductIndex::ID, $criteria->getIds())
            );
        }

        if ($criteria->getCompanyId()) {
            $query->addFilter(
                new Query\Term([ProductIndex::COMPANY_ID => ['value' => $criteria->getCompanyId()]])
            );
        }
    }

    public function getBodyForChangeSet(ChangeSet $changeSet): array
    {
        if (!$changeSet instanceof ProductChangeSet) {
            throw new \InvalidArgumentException();
        }

        //NB! fields processing must be in sync with \Metal\ProductsBundle\Indexer\ProductsProvider::transformResult

        $body = [];
        if ($changeSet->isFieldChanged(ProductChangeSet::IS_SPECIAL_OFFER)) {
            $body[ProductIndex::IS_SPECIAL_OFFER] = $changeSet->getIsSpecialOffer();
        }

        if ($changeSet->isFieldChanged(ProductChangeSet::IS_HOT_OFFER)) {
            $body[ProductIndex::IS_HOT_OFFER] = $changeSet->getIsHotOffer();
        }

        if ($changeSet->isFieldChanged(ProductChangeSet::UPDATED_AT)) {
            $body[ProductIndex::DAY_UPDATED_AT] = $changeSet->getUpdatedAt()->modify('midnight')->format('Ymd');
        }

        if ($changeSet->isFieldChanged(ProductChangeSet::COMPANY_LAST_VISITED_AT)) {
            $body[ProductIndex::COMPANY_LAST_VISITED_AT] = $changeSet->getCompanyLastVisitedAt()->getTimestamp();
        }

        if ($changeSet->isFieldChanged(ProductChangeSet::COMPANY_RATING)) {
            $body[ProductIndex::COMPANY_RATING] = $changeSet->getCompanyRating();
        }

        return $body;
    }
}
