<?php

namespace Metal\ProductsBundle\DataFetching\Elastica\Aggregator;

use Elastica\Aggregation\Cardinality;
use Elastica\Aggregation\ValueCount;
use Elastica\Query;
use Elastica\ResultSet;
use Metal\ProductsBundle\DataFetching\DataRequest;
use Metal\ProductsBundle\DataFetching\Spec\Aggregation\CountAggregation;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\DataFetching\Elastica\AbstractAggregator;
use Metal\ProjectBundle\DataFetching\Result\Aggregation\CountAggregationResult;
use Metal\ProjectBundle\DataFetching\Spec\Aggregation\Aggregation;

class CountAggregator extends AbstractAggregator
{
    public function buildAggregation(Query $query, DataRequest $dataRequest, Aggregation $aggregation): bool
    {
        if (!$aggregation instanceof CountAggregation) {
            return false;
        }

        /** @var ProductsFilteringSpec $filteringSpec */
        $filteringSpec = $dataRequest->getFilteringSpec(ProductsFilteringSpec::class);
        $aggName = $this->getAggregationName($aggregation);

        if ($filteringSpec->loadCompanies) {
            $countAggregation = new Cardinality($aggName);
            $countAggregation->setField('company_id');
        } else {
            $countAggregation = new ValueCount($aggName, '_uid');
        }

        $query->addAggregation($countAggregation);

        return true;
    }

    public function processAggregationResult(array $aggregations, ResultSet $resultSet): array
    {
        $aggregationResults = [];
        foreach ($aggregations as $aggName => $agg) {
            $aggregationResults[$aggName] = new CountAggregationResult($agg['value']);
        }

        return $aggregationResults;
    }
}
