<?php

namespace Metal\DemandsBundle\DataFetching\Spec;

use Metal\ProjectBundle\DataFetching\Spec\LoadingSpec;

class DemandLoadingSpec implements LoadingSpec
{
    public $attachDemandItem = true;

    public $attachDemandFiles = true;

    public $attachCategories = true;

    public $attachCitiesAndRegions = false;

    public $attachFavorite = true;

    public $categoryId;

    public function attachDemandItem($attachDemandItem, $categoryIdByWhichLoad = null)
    {
        $this->attachDemandItem = $attachDemandItem;
        if ($categoryIdByWhichLoad) {
            $this->categoryId = $categoryIdByWhichLoad;
        }

        return $this;
    }

    public function attachCategories($attachCategories)
    {
        $this->attachCategories = $attachCategories;

        return $this;
    }

    public function attachDemandFiles($attachDemandFiles)
    {
        $this->attachDemandFiles = $attachDemandFiles;

        return $this;
    }

    public function attachFavorite($attach)
    {
        $this->attachFavorite = $attach;

        return $this;
    }

    public function attachCitiesAndRegions($attachCitiesAndRegions)
    {
        $this->attachCitiesAndRegions = $attachCitiesAndRegions;

        return $this;
    }
}
