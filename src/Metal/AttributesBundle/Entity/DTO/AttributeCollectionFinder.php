<?php

namespace Metal\AttributesBundle\Entity\DTO;

use Metal\AttributesBundle\Entity\Attribute;
use Metal\AttributesBundle\Entity\AttributeValue;

class AttributeCollectionFinder
{
    private $attributesCollection;

    /**
     * @var AttributeValue[]
     */
    private $attributeValuesBySlug = [];

    public function __construct(AttributesCollection $attributesCollection)
    {
        $this->attributesCollection = $attributesCollection;
        foreach ($attributesCollection->getAttributesValues() as $attributeValue) {
            $this->attributeValuesBySlug[$attributeValue->getSlug()] = $attributeValue;
        }
    }

    /**
     * @param string $code
     *
     * @return Attribute|null
     */
    public function findAttributeByCode($code)
    {
        foreach ($this->attributesCollection->getAttributes() as $attribute) {
            if ($attribute->getCode() === $code) {
                return $attribute;
            }
        }
    }

    /**
     * @param Attribute $attribute
     * @param string $slug
     *
     * @return AttributeValue|null
     */
    public function findAttributeValueByAttributeAndSlug(Attribute $attribute, $slug)
    {
        if (isset($this->attributeValuesBySlug[$slug])
            && $this->attributeValuesBySlug[$slug]->getAttribute() === $attribute) {
            return $this->attributeValuesBySlug[$slug];
        }
    }
}
