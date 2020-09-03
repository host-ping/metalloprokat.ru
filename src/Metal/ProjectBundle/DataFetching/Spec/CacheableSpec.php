<?php

namespace Metal\ProjectBundle\DataFetching\Spec;

interface CacheableSpec
{
    /**
     * @return string|null Returns null if this specification is not cacheable
     */
    public function getCacheKey();
}
