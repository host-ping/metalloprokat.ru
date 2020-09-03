<?php

namespace Brouzie\Components\Indexer\Serializer;

use Brouzie\Components\Indexer\Entry;
use Brouzie\Components\Indexer\Entry\GenericEntry;
use Brouzie\Components\Indexer\Normalizer;

class SimpleArrayNormalizer implements Normalizer
{
    private $idField;

    public function __construct(string $idField)
    {
        $this->idField = $idField;
    }

    public function normalize($object): Entry
    {
        return new GenericEntry($object[$this->idField], $object);
    }
}
