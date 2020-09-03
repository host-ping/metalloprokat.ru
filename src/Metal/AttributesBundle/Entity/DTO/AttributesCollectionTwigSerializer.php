<?php

namespace Metal\AttributesBundle\Entity\DTO;

class AttributesCollectionTwigSerializer
{
    private $attributesCollection;

    public function __construct(AttributesCollection $attributesCollection)
    {
        $this->attributesCollection = $attributesCollection;
    }

    public function __toString()
    {
        return $this->attributesCollection->toString(', ');
    }

    public function __call($attributeCode, array $args = [])
    {
        foreach ($this->attributesCollection->getAttributes() as $attribute) {
            if ($attribute->getCode() === $attributeCode) {
                return $this->attributesCollection->toStringByAttribute($attribute);
            }
        }

        return '';
    }
}
