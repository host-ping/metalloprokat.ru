<?php

namespace Metal\ProductsBundle\DataFetching\Spec;

use Metal\ProjectBundle\DataFetching\Spec\LoadingSpec;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\TerritoryInterface;

class ProductsLoadingSpec implements LoadingSpec
{
    public $preloadImages = true;

    public $attachFavorite = true;

    public $attachCategories = true;

    public $preloadPhones;

    public $normalizePrice;

    public $preloadDelivery;

    public $trackShowing = false;

    public $attachProductsAttr;

    public function preloadImages($preload)
    {
        $this->preloadImages = $preload;

        return $this;
    }

    public function attachFavorite($attach)
    {
        $this->attachFavorite = $attach;

        return $this;
    }

    public function attachCategories($attach)
    {
        $this->attachCategories = $attach;

        return $this;
    }

    public function preloadPhones(TerritoryInterface $territory)
    {
        $this->preloadPhones = $territory;

        return $this;
    }

    public function normalizePrice(Country $country)
    {
        $this->normalizePrice = $country->getId();

        return $this;
    }

    public function preloadDelivery(TerritoryInterface $territory)
    {
        $this->preloadDelivery = $territory;

        return $this;
    }

    public function trackShowing($sourceTypeId)
    {
        $this->trackShowing = $sourceTypeId;

        return $this;
    }

    public function attachProductsAttr($attachProducts, $matchQuery = '')
    {
        $this->attachProductsAttr = array('attach' => $attachProducts);
        if ($matchQuery) {
            $this->attachProductsAttr['query'] = $matchQuery;
        }

        return $this;
    }
}
