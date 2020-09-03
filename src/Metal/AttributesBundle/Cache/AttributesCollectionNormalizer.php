<?php

namespace Metal\AttributesBundle\Cache;

use Metal\AttributesBundle\Entity\DTO\AttributesCollection;

class AttributesCollectionNormalizer
{
    public function __invoke($object)
    {
        if ($object instanceof AttributesCollection) {
            return array(AttributesCollection::class => $object->toArray());
        }
    }
}
