<?php

namespace Metal\ProductsBundle\Indexer\Elastica;

use Brouzie\Components\Indexer\Entry;
use Brouzie\Components\Indexer\Entry\GenericEntry;
use Brouzie\Components\Indexer\Normalizer;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Region;

class ElasticaProductNormalizer implements Normalizer
{
    public function normalize($object): Entry
    {
        $object['cities_ids'] = array_map(
            [City::class, 'getESVirtualCityIdForVirtualCityId'],
            $object['cities_ids']
        );

        $object['regions_ids'] = array_map(
            [Region::class, 'getESVirtualRegionIdForVirtualRegionId'],
            $object['regions_ids']
        );

        //FIXME: по-моему сюда что-то не то записывается, может быть нужно умножать на 1000
    //                'company_last_visited_at' => $object['company_last_visited_at'],

        return new GenericEntry($object['id'], $object);
    }
}
