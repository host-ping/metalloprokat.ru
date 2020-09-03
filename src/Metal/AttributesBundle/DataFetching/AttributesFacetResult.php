<?php

namespace Metal\AttributesBundle\DataFetching;

use Brouzie\Sphinxy\Query\MultiResultSet;
use Brouzie\Sphinxy\Query\SimpleResultSet;
use Metal\AttributesBundle\Entity\Attribute;

class AttributesFacetResult implements \IteratorAggregate
{
    const RESULT_SET_NAME_PREFIX = 'attributes.';
    /**
     * @var array An array of (Attribute.id => (AttributeValue.id => %items count from sphinx%))
     */
    protected $attributes = array();

    /**
     * AttributesFacetResult constructor.
     *
     * @param MultiResultSet $facetsResultSet
     * @param Attribute[]|null|array $attributes An array of (Attribute.id => resultSetName)
     */
    public function __construct(MultiResultSet $facetsResultSet, $attributes = null)
    {
        if (null === $attributes) {
            foreach ($facetsResultSet as $resultSetName => $resultSet) {
                if (0 !== strpos($resultSetName, self::RESULT_SET_NAME_PREFIX)) {
                    continue; // skip non-related result set
                }
                $attributeId = substr($resultSetName, strlen(self::RESULT_SET_NAME_PREFIX));
                $this->processResultSet($resultSet, $attributeId, $resultSetName);
            }
        } else {
            foreach ($attributes as $key => $attribute) {

                if ($attribute instanceof Attribute) {
                    $attributeId = $attribute->getId();
                    $resultSetName = self::RESULT_SET_NAME_PREFIX.$attributeId;
                } else {
                    $attributeId = $key;
                    $resultSetName = $attribute;
                }

                if (!$facetsResultSet->hasResultSet($resultSetName)) {
                    continue;
                }

                $resultSet = $facetsResultSet->getResultSet($resultSetName);
                $this->processResultSet($resultSet, $attributeId, $resultSetName);
            }
        }
    }

    public function getIterator()
    {
        foreach ($this->attributes as $attributeId => $itemsCountPerAttributeValue) {
            yield $attributeId => $itemsCountPerAttributeValue;
        }
    }

    public function eachAttributeValuesIds()
    {
        foreach ($this->attributes as $attributeId => $itemsCountPerAttributeValue) {
            foreach ($itemsCountPerAttributeValue as $attributeValueId => $itemsCount) {
                yield $attributeValueId;
            }
        }
    }

    protected function processResultSet(SimpleResultSet $resultSet, $attributeId, $column)
    {
        foreach ($resultSet as $row) {
            $attributeValueId = $row[$column];
            if ($attributeValueId === null) {
                continue; // skip null values
            }
            $this->attributes[$attributeId][$attributeValueId] = $row['count(*)'];
        }
    }
}
