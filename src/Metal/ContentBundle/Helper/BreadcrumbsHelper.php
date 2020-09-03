<?php

namespace Metal\ContentBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\ContentBundle\Entity\AbstractContentEntry;

class BreadcrumbsHelper extends HelperAbstract
{
    public function getBreadcrumbsForContentEntry(AbstractContentEntry $contentEntry)
    {
        $productBreadcrumb = array(
            'id' => $contentEntry->getId(),
            'title' => $contentEntry->getTitle(),
            'is_label' => true,
        );

        return array($productBreadcrumb);
    }
}