<?php

namespace Metal\ProjectBundle\DataFetching;

use Metal\ProjectBundle\DataFetching\Spec\CacheableSpec;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\CacheItem;

class CacheProfile
{
    private $key;

    private $ttl;

    private $tags = array();

    /**
     * @param string $key
     * @param int|null $ttl
     * @param array $tags
     */
    public function __construct($key, $ttl = null, array $tags = array())
    {
        $this->key = $key;
        $this->ttl = $ttl;
        $this->tags = $tags;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getTtl()
    {
        return $this->ttl;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function configureCacheItem(CacheItemInterface $item)
    {
        if (0 !== $this->ttl) {
            $item->expiresAfter($this->ttl);
        }

        if ($item instanceof CacheItem && $this->tags) {
            $item->tag($this->tags);
        }
    }

    /**
     * @param CacheableSpec[] $specifications
     * @param string|null $pattern
     * @param array $keyParts
     *
     * @return string|null
     */
    public static function getKeyFromSpecifications(array $specifications, array $keyParts = array(), $pattern = null)
    {
        foreach ($specifications as $specification) {
            if (!$specification instanceof CacheableSpec) {
                return null;
            }

            if (!$cacheKey = $specification->getCacheKey()) {
                return null;
            }
            $keyParts[] = $cacheKey;
        }

        $key = sha1(serialize($keyParts));

        return null === $pattern ? $key : sprintf($pattern, $key);
    }
}
