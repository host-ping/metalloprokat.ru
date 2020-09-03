<?php

namespace Metal\ProjectBundle\DataFetching\Sphinxy;

use Brouzie\Sphinxy\Query\MultiResultSet;

class FacetResultExtractor
{
    /**
     * @var array An array of (faceted item id => %items count from sphinx% ))
     */
    protected $rows = array();

    public function __construct(MultiResultSet $facetsResultSet, $column)
    {
        if ($facetsResultSet->hasResultSet($column)) {
            foreach ($facetsResultSet->getResultSet($column) as $row) {
                $this->rows[$row[$column]] = $row['count(*)'];
            }
        }
    }

    public function getCounts()
    {
        return $this->rows;
    }

    public function getIds()
    {
        return array_keys($this->rows);
    }
}
