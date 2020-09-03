<?php

namespace Metal\ProductsBundle\Indexer\Sphinxy;

use Brouzie\Components\Indexer\Entry;
use Brouzie\Components\Indexer\Entry\GenericEntry;
use Brouzie\Components\Indexer\Normalizer;

class SphinxyProductNormalizer implements Normalizer
{
    public function normalize($object): Entry
    {
        $object['priority_show_territorial'] = json_encode($object['priority_show_territorial']);
        $object['attributes'] = json_encode($object['attributes']);

        $object['company_title_field'] = $object['company_title'].' ';
        $object['product_title'] = $object['title'].' ';

        return new GenericEntry($object['id'], $object);
    }
}
