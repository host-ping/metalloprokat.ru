<?php

namespace Metal\AttributesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\AttributesBundle\Entity\Attribute;
use Metal\CatalogBundle\Entity\Product;
use Metal\CategoriesBundle\Entity\Category;

class AttributeCategoryRepository extends EntityRepository
{
    private $cache = [];

    public function refreshAttributeCategory()
    {
        $conn = $this->_em->getConnection();

        $conn->executeUpdate(
            'TRUNCATE attribute_category'
        );

        $conn->executeUpdate(
            'INSERT INTO attribute_category (attribute_id, category_id)
            SELECT av.attribute_id, avc.category_id FROM attribute_value_category AS avc
            JOIN attribute_value AS av ON avc.attribute_value_id = av.id
            GROUP BY av.attribute_id, avc.category_id'
        );
    }

    public function getAdditionalAttributesForCategory($category)
    {
        $attributes = $this
            ->createQueryBuilder('ac')
            ->select('a.id, a.title, a.code')
            ->join('ac.attribute', 'a')
            ->andWhere('ac.category = :category')
            ->setParameter('category', $category)
            ->orderBy('a.title')
            ->getQuery()
            ->getResult();

        $attributes = array_column($attributes, 'title', 'code');
        unset($attributes[Product::ATTR_CODE_BRAND], $attributes[Product::ATTR_CODE_MANUFACTURER]);

        return $attributes;
    }

    /**
     * @param Category|int $category
     *
     * @return Attribute[]
     */
    public function getAttributesForCategory($category)
    {
        $categoryId = $category instanceof Category ? $category->getRealCategoryId() : $category;

        if (isset($this->cache[$categoryId])) {
            return $this->cache[$categoryId];
        }

        $attributes = $this
            ->_em
            ->createQueryBuilder()
            ->select('a')
            ->from('MetalAttributesBundle:Attribute', 'a')
            ->join('MetalAttributesBundle:AttributeCategory', 'ac', 'WITH', 'ac.attribute = a.id')
            ->andWhere('ac.category = :category')
            ->setParameter('category', $categoryId)
            ->orderBy('a.outputPriority')
            ->getQuery()
            ->getResult();

        return $this->cache[$categoryId] = $attributes;
    }
}
