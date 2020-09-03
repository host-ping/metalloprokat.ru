<?php

namespace Metal\ProjectBundle\DataFetching\Spec;

interface TaggableCacheableSpec extends CacheableSpec
{
    /**
     * @return string[]
     */
    public function getCacheTags();
}
