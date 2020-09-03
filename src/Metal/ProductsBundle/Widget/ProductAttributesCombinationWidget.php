<?php

namespace Metal\ProductsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProjectBundle\Helper\SeoHelper;

class ProductAttributesCombinationWidget extends WidgetAbstract
{
    protected function setDefaultOptions()
    {
        $this->optionsResolver
            ->setRequired(
                array(
                    'attributes_collection',
                    'category',
                    'company',
                )
            )
            ->setAllowedTypes('attributes_collection', AttributesCollection::class);
    }

    public function getParametersToRender()
    {
        $category = $this->options['category'];
        /* @var $category Category */
        $company = $this->options['company'];
        $attributesCollection = $this->options['attributes_collection'];
        /* @var $attributesCollection AttributesCollection */

        $productAttrSets = array();
        $productAttrExtendedSets = array();
        $markTitle = $markSlug = '';
        $seoHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Seo');
        /* @var $seoHelper SeoHelper */

        $usedAttributesCombinations = $seoHelper->getAttributeValuesCombination($attributesCollection);

        $attributesCollectionWithMark = new AttributesCollection();
        $attributeValues = $attributesCollection->getAttributesValues();

        foreach ($attributeValues as $attributeValue) {
            $slug = $attributeValue->getSlug();
            $code = $attributeValue->getAttribute()->getCode();
            if ($code !== 'size' && !isset($usedAttributesCombinations[$slug])) {
                $productAttrSets[] = array(
                    'title' => $category->getTitle().' '.$attributeValue->getValue(),
                    'slug' => $category->getSlugCombined().'/'.$slug,
                );
            }

            if ($code === 'mark') {
                $markTitle = $attributeValue->getValue();
                $attributesCollectionWithMark->appendAttributeValue($attributeValue);
            }
        }

        if ($markTitle) {
            foreach ($attributeValues as $attributeValue) {
                $code = $attributeValue->getAttribute()->getCode();
                $slugCombined = $attributesCollectionWithMark->getUrl($attributeValue);
                if ($code !== 'mark' && $code !== 'size' && !isset($usedAttributesCombinations[$slugCombined])) {
                    $productAttrExtendedSets[] = array(
                        'title' => $category->getTitle().' '.$markTitle.' '.$attributeValue->getValue(),
                        'slug' => $category->getUrl($slugCombined),
                    );
                }
            }
        }

        return compact('productAttrSets', 'productAttrExtendedSets', 'category', 'company');
    }
}
