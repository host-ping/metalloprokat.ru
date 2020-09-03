<?php

namespace Metal\ProjectBundle\Imagine;

use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class ImagineCacheManager extends CacheManager
{
    public function getBrowserPath($path, $filter, array $runtimeConfig = array(), $resolver = null)
    {
        if (!empty($runtimeConfig)) {

            return $this->generateUrl($path, $filter, $runtimeConfig, $resolver);
        }

        return $this->generateUrl($path, $filter, array(), $resolver);
    }
}
