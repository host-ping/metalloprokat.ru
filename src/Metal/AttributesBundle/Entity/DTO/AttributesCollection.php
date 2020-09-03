<?php

namespace Metal\AttributesBundle\Entity\DTO;

use Metal\AttributesBundle\Entity\Attribute;
use Metal\AttributesBundle\Entity\AttributeValue;

class AttributesCollection implements \IteratorAggregate, \Countable, \ArrayAccess
{
    /**
     * @var Attribute[]
     */
    private $attributes = array();

    private $attributeValues = array();

    public function appendAttributeValue(AttributeValue $attributeValue)
    {
        $attribute = $attributeValue->getAttribute();
        $this->attributes[$attribute->getId()] = $attribute;
        $this->attributeValues[$attribute->getId()][$attributeValue->getId()] = $attributeValue;
    }

    /**
     * @param AttributeValue[] $attributeValues
     */
    public function appendAttributeValues($attributeValues)
    {
        foreach ($attributeValues as $attributeValue) {
            $this->appendAttributeValue($attributeValue);
        }
    }

    public function containsAttribute(Attribute $attribute)
    {
        return isset($this->attributes[$attribute->getId()]);
    }

    public function containsAttributeValue(AttributeValue $attributeValue)
    {
        return isset($this->attributeValues[$attributeValue->getAttribute()->getId()][$attributeValue->getId()]);
    }

    public function getSlugs()
    {
        return preg_split('#[/_]#', $this->getUrl());
    }

    /**
     * @return Attribute[]
     */
    public function getAttributes()
    {
        return array_values($this->attributes);
    }

    /**
     * @return AttributeValue[]
     */
    public function getAttributesValues()
    {
        $attributesValues = array();
        foreach ($this as $attribute => $attributeValues) {
            foreach ($attributeValues as $attributeValue) {
                $attributesValues[] = $attributeValue;
            }
        }

        return $attributesValues;
    }

    public function getAttributesValuesIds()
    {
        $attributesValuesIds = array();
        foreach ($this->getAttributesValues() as $attributeValue) {
            $attributesValuesIds[] = $attributeValue->getId();
        }

        return $attributesValuesIds;
    }

    public function toArray()
    {
        $attributesValuesIds = array();
        foreach ($this as $attribute => $attributeValues) {
            foreach ($attributeValues as $attributeValue) {
                $attributesValuesIds[$attribute->getId()][] = $attributeValue->getId();
            }
        }

        return $attributesValuesIds;
    }

    public function getUrl(AttributeValue $include = null, AttributeValue $exclude = null)
    {
        $slugGroups = array();
        $inserted = null === $include;
        foreach ($this as $attribute => $attributeValues) {
            /* @var $attribute Attribute */
            /* @var $attributeValues AttributeValue[] */
            foreach ($attributeValues as $attributeValue) {
                if ($exclude == $attributeValue) {
                    continue;
                }

                if (!$inserted) {
//                    clear_buffer();
//                    vd(
//                        $include->getAttribute()->getUrlPriority(),
//                        $attributeValue->getAttribute()->getUrlPriority(),
//                        $include->getUrlPriority(),
//                        $attributeValue->getUrlPriority(),
//                        $include->getSlug(),
//                        $attributeValue->getSlug()
//                    );

                    if ($include->getAttribute()->getUrlPriority() < $attributeValue->getAttribute()->getUrlPriority()) {
                        $slugGroups[$include->getAttribute()->getId()][] = $include->getSlug();
                        $inserted = true;
                    } elseif ($include->getAttribute() == $attributeValue->getAttribute() && $include->getUrlPriority() <= $attributeValue->getUrlPriority()) {
                        $slugGroups[$include->getAttribute()->getId()][] = $include->getSlug();
                        $inserted = true;
                    }
                }

                $slugGroups[$attribute->getId()][] = $attributeValue->getSlug();
            }
        }

        //TODO: обрабатывать ситуацию, когда у нас есть в коллекции гост и марка, а мы вставляем размер, и размер должен вставиться между гостами и марками
        if (!$inserted) {
            $slugGroups[$include->getAttribute()->getId()][] = $include->getSlug();
        }

        return implode(
            '/',
            array_map(
                function ($items) {
                    return implode('_', $items);
                },
                $slugGroups
            )
        );
    }

    public function toStringAttributes($attrGlue = ', ', $field = 'title')
    {
        $items = [];
        /** @var Attribute $attribute */
        foreach ($this as $attribute => $attributeValues) {
            switch ($field) {
                case 'title':
                    $item = $attribute->getTitle();
                    break;

                default:
                    throw new \InvalidArgumentException();
            }

            $items[] = $item;
        }

        return implode($attrGlue, $items);
    }

    public function toStringByAttribute(Attribute $attribute, $attrValGlue = ' и ', $field = 'value', $useSuffix = false)
    {
        /** @var AttributeValue[] $attributeValues */
        $attributeValues = $this[$attribute];

        $items = array();
        foreach ($attributeValues as $attributeValue) {
            switch ($field) {
                case 'value':
                    $item = $attributeValue->getValue();
                    break;

                case 'valueAccusative':
                    $item = $attributeValue->getValueAccusative() ?: $attributeValue->getValue();
                    break;

                case 'valueAccusativeForEmbed':
                    $item = $attributeValue->getValueAccusativeForEmbed() ?: $attributeValue->getValueForEmbed();
                    break;

                default:
                    throw new \InvalidArgumentException();
            }

            $items[] = $item.($useSuffix ? $attribute->getSuffix() : '');
        }

        return implode($attrValGlue, $items);
    }

    public function toString($attrValGlue = ' и ', $attrGlue = ', ', $field = 'value', $useSuffix = false)
    {
        $attrValues = array();
        /** @var Attribute $attribute */
        foreach ($this as $attribute => $attributeValues) {
            $attrValues[] = $this->toStringByAttribute($attribute, $attrValGlue, $field, $useSuffix);
        }

        return implode($attrGlue, $attrValues);
    }

    public function getIterator()
    {
        foreach ($this->attributes as $attributeId => $attribute) {
            yield $attribute => $this->attributeValues[$attributeId];
        }
    }

    public function count()
    {
        return count($this->attributes);
    }

    public function offsetExists($offset)
    {
        self::validateOffsetType($offset);

        return isset($this->attributeValues[$offset->getId()]);
    }

    /**
     * @return AttributeValue[]
     */
    public function offsetGet($offset)
    {
        self::validateOffsetType($offset);

        return $this->attributeValues[$offset->getId()];
    }

    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Use "appendAttributeValue" instead.');
    }

    public function offsetUnset($offset)
    {
        self::validateOffsetType($offset);

        unset($this->attributes[$offset->getId()], $this->attributeValues[$offset->getId()]);
    }

    private static function validateOffsetType($offset)
    {
        if (!$offset instanceof Attribute) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expected object of class "%s" as key, "%s" given.',
                    Attribute::class,
                    gettype($offset)
                )
            );
        }
    }
}
