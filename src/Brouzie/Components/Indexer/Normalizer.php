<?php

namespace Brouzie\Components\Indexer;

interface Normalizer
{
    /**
     * @param mixed $object
     */
    public function normalize($object): Entry;
}
