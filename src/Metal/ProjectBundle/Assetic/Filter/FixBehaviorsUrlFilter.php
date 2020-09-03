<?php

namespace Metal\ProjectBundle\Assetic\Filter;

use Assetic\Asset\AssetInterface;
use Assetic\Filter\FilterInterface;
use Metal\MiniSiteBundle\Service\MinisiteCssCompiler;

class FixBehaviorsUrlFilter implements FilterInterface
{
    /**
     * {@inheritdoc}
     */
    public function filterLoad(AssetInterface $asset)
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function filterDump(AssetInterface $asset)
    {
        $asset->setContent(MinisiteCssCompiler::fixBehaviorsUrl($asset->getContent()));
    }
}
