<?php

namespace Metal\ProjectBundle\Util;

class ArrayUtil
{
    /**
     * Unique, truncate and merge GROUP_CONCAT result.
     *
     * @param array|\Traversable $rows
     * @param string $column
     * @param int $limit
     *
     * @return array
     */
    public static function mergeGroupConcatResult($rows, $column, $limit)
    {
        $ids = array();
        foreach ($rows as $row) {
            $ids = array_merge($ids, array_slice(array_unique(explode(',', $row[$column])), 0, $limit));
        }

        return $ids;
    }
}
