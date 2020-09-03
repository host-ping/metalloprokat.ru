<?php

namespace Metal\AttributesBundle\DataFetching\Spec;

use Metal\AttributesBundle\Entity\Attribute;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;

trait AttributeSpec
{
    /**
     * @var array An array of (Attribute.id => AttributeValue.id[]) structure
     */
    public $productAttrsByGroup = [];

    public function attributesCollection(AttributesCollection $attributesCollection)
    {
        $productAttrsByGroup = [];
        /** @var AttributeValue[] $attributeValues */
        /** @var Attribute $attribute */
        foreach ($attributesCollection as $attribute => $attributeValues) {
            foreach ($attributeValues as $attributeValueId => $attributeValue) {
                $productAttrsByGroup[$attribute->getId()][] = $attributeValue->getId();
            }
        }

        $this->productAttrsByGroup($productAttrsByGroup);

        return $this;
    }

    public function productAttrs($productAttrs)
    {
        if ($productAttrs) {
            $attrsByGroup = array();
            foreach ($productAttrs as $attr) {
                $attrsByGroup[$attr['parameterGroup']['typeId']][] = $attr['parameterOption']['id'];
            }
            $this->productAttrsByGroup = $attrsByGroup;
        }

        return $this;
    }

    public function productAttrsByGroup($attrsByGroup)
    {
        $this->productAttrsByGroup = $attrsByGroup ?: [];

        return $this;
    }

    public function resetAttributesFilter()
    {
        $this->productAttrsByGroup = [];
    }
}
