<?php

namespace Metal\ProjectBundle\DataFetching\Util;

use Metal\ProjectBundle\DataFetching\CacheProfile;
use Metal\ProjectBundle\DataFetching\Spec\CacheableSpec;
use Metal\ProjectBundle\DataFetching\Spec\TaggableCacheableSpec;

class CacheUtil
{
    /**
     * @param CacheProfile|int|false|null $cache false for force no cache, int for cache ttl, null for default cache ttl
     * @param CacheableSpec[] $specifications
     */
    public static function getCacheProfile(
        $cache,
        array $specifications,
        string $pattern,
        array $keyParts = []
    ): ?CacheProfile {
        if (false === $cache) {
            return null;
        }

        if ($cache instanceof CacheProfile) {
            //TODO: merge tags from specs?
            return $cache;
        }

        if (is_int($cache) || null === $cache) {
            if (!$key = CacheProfile::getKeyFromSpecifications($specifications, $keyParts, $pattern)) {
                return null;
            }

            $tags = [[]];
            foreach ($specifications as $specification) {
                if ($specification instanceof TaggableCacheableSpec) {
                    $tags[] = $specification->getCacheTags();
                }
            }
            $tags = array_merge(...$tags);

            return new CacheProfile($key, $cache, $tags);
        }

        throw new \InvalidArgumentException('Expected CacheProfile|int|false|null.');
    }
}
