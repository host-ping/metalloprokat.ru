<?php

namespace Metal\AttributesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\AttributesBundle\Entity\AttributeValueCategory;
use Metal\CategoriesBundle\Entity\Category;

class AttributeValueCategoryRepository extends EntityRepository
{
    private $identityMap = array();

    public function getSimpleAttributeValuesForCategory(Category $category)
    {
        $attributeValues = $this
            ->createQueryBuilder('avc')
            ->addSelect('attributeValue.id AS attributeValueId')
            ->addSelect('attributeValue.value AS attributeValueValue')
            ->addSelect('IDENTITY(attributeValue.attribute) AS attributeId')
            ->join('avc.attributeValue', 'attributeValue')
            ->andWhere('avc.category = :category')
            ->setParameter('category', $category)
            ->orderBy('attributeValue.outputPriority')
            ->getQuery()
            ->getResult();

        $result = array();
        foreach ($attributeValues as $attributeValue) {
            $result[$attributeValue['attributeId']][] = array(
                'id' => $attributeValue['attributeValueId'],
                'title' => $attributeValue['attributeValueValue']
            );
        }

        return $result;
    }

    public function getAttributesOptionsArray($attrCode, $categoryId = null)
    {
        $attributesQb = $this->_em->createQueryBuilder()
            ->select('av.id, av.value')
            ->from('MetalAttributesBundle:AttributeValueCategory', 'avc')
            ->join('avc.attributeValue', 'av')
            ->join('av.attribute', 'a')
            ->where('a.code = :code')
            ->setParameter('code', $attrCode)
            ->orderBy('av.value', 'ASC');

        if ($categoryId) {
            $attributesQb
                ->andWhere('avc.category = :category_id')
                ->setParameter('category_id', $categoryId);
        }

        $attributes = $attributesQb
            ->getQuery()
            ->getResult();

        return array_column($attributes, 'value', 'id');
    }

    public function getAttributesOptionsWithoutCodeArray($categoryId = null)
    {
        $attributesQb = $this->_em->createQueryBuilder()
            ->select('av.id, av.value')
            ->from('MetalAttributesBundle:AttributeValueCategory', 'avc')
            ->join('avc.attributeValue', 'av')
            ->join('av.attribute', 'a')
            ->orderBy('av.value', 'ASC');

        if ($categoryId) {
            $attributesQb
                ->andWhere('avc.category = :category_id')
                ->setParameter('category_id', $categoryId);
        }

        $attributes = $attributesQb
            ->getQuery()
            ->getResult();

        return array_column($attributes, 'value', 'id');
    }

    /**
     * @param AttributeValue $attributeValue
     * @param Category $category
     *
     * @return AttributeValueCategory
     */
    public function findOrCreateAttributeValueCategory(AttributeValue $attributeValue, Category $category)
    {
        $attributeValueCategory = $this->findOneBy(array('attributeValue' => $attributeValue, 'category' => $category));
        if ($attributeValueCategory) {
            return $attributeValueCategory;
        }

        $attributeValueCategory = new AttributeValueCategory();
        $attributeValueCategory->setAttributeValue($attributeValue);
        $attributeValueCategory->setCategory($category);

        $this->_em->persist($attributeValueCategory);

        return $attributeValueCategory;
    }

    /**
     * @param null $categoryId
     * @return AttributeValue[]
     */
    public function getAttributeValuesByCategory($categoryId = null)
    {
        if (!$categoryId) {
            return array();
        }

        if (isset($this->identityMap[$categoryId])) {
            return $this->identityMap[$categoryId];
        }

        return $this->identityMap[$categoryId] = $this->_em->createQueryBuilder()
            ->select('attributeValue')
            ->addSelect('attribute')
            ->from('MetalAttributesBundle:AttributeValue', 'attributeValue')
            ->join('attributeValue.attribute', 'attribute')
            ->join('MetalAttributesBundle:AttributeValueCategory', 'attributeValueCategory', 'WITH', 'attributeValueCategory.attributeValue = attributeValue.id')
            ->where('attributeValueCategory.category = :category_id')
            ->orderBy('attribute.outputPriority', 'DESC')
            ->addOrderBy('attributeValue.outputPriority', 'DESC')
            ->setParameter('category_id', $categoryId)
            ->getQuery()
            ->getResult();
    }
}
