<?php

namespace Metal\ProjectBundle\DataFetching;

class UnsupportedSpecException extends \InvalidArgumentException
{
    public static function create($expectedClass, $objectGiven)
    {
        return new self(sprintf('Expected "%s" spec class, "%s" given.', $expectedClass, get_class($objectGiven)));
    }
}
