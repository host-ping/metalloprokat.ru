<?php

namespace Metal\ProjectBundle\DataFetching\Spec;

class FacetSpec implements CacheableSpec
{
    protected const RESULTS_COUNT_UNLIMITED = 2500;

    /**
     * @readonly
     * @var array
     */
    public $facets = [];

    public function facetBy(array $facetOptions)
    {
        $facetOptions = array_merge(
            array(
                'name' => null,
                'column' => null,
                'order' => 'COUNT(*)',
                'direction' => 'DESC',
                'limit' => null,
                'skip' => 0,
                'criteria' => null,
            ),
            $facetOptions
        );
        $facetOptions['limit'] = $this->getLimit($facetOptions);

        $this->facets[] = $facetOptions;

        return $this;
    }

    public function getCacheKey()
    {
        return sha1(serialize($this->facets));
    }

    private function getLimit($facetOptions)
    {
        return $facetOptions['limit'] ?? self::RESULTS_COUNT_UNLIMITED;
    }
}
