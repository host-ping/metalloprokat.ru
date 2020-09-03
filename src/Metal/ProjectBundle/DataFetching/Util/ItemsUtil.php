<?php

namespace Metal\ProjectBundle\DataFetching\Util;

use Metal\ProjectBundle\DataFetching\Result\Item;

class ItemsUtil
{
    /**
     * @param Item[] $items
     */
    public static function getIdsOfItems(iterable $items): array
    {
        $ids = [];
        foreach ($items as $item) {
            $ids[] = $item->getId();
        }

        return $ids;
    }

    public static function extractPropertyValues(iterable $items, string $property): array
    {
        $values = [];
        foreach ($items as $item) {
            $values[] = $item->$property;
        }

        return $values;
    }
}
