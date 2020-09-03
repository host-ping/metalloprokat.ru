<?php

namespace Metal\ContentBundle\DataFetching\Spec;

use Metal\ProjectBundle\DataFetching\Spec\LoadingSpec;

class ContentEntryLoadingSpec implements LoadingSpec
{
    public $attachTags = true;

    public function attachTags($attach)
    {
        $this->attachTags = $attach;

        return $this;
    }
}
