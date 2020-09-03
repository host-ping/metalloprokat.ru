<?php

namespace Metal\ProductsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\AttributesBundle\Entity\DTO\LightAttributeValue;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\ProductsBundle\Entity\Product;

class ProductAttributeValueRepository extends EntityRepository
{
    /**
     * @param Product[]|\Traversable $products
     */
    public function attachAttributesCollectionToProducts($products)
    {
        $productParameterValues = $this->_em
            ->getRepository('MetalProductsBundle:ProductParameterValue')
            ->createQueryBuilder('ppv')
            ->select('IDENTITY(ppv.product) as productId, IDENTITY(ppv.parameterOption) as parameterOptionId')
            ->andWhere('ppv.product IN (:products)')
            ->setParameter('products', $products)
            ->getQuery()
            ->getResult();

        $attributeValueIds = array();
        $productsPerParameterOptions = array();
        foreach ($productParameterValues as $productParameterValue) {
            $attributeValueIds[$productParameterValue['parameterOptionId']] = true;
            $productsPerParameterOptions[$productParameterValue['parameterOptionId']][] = $productParameterValue['productId'];
        }

        $attributeValues = $this->_em->createQueryBuilder()
            ->addSelect(LightAttributeValue::getCreateDQL())
            ->from('MetalAttributesBundle:AttributeValue', 'av')
            ->join('av.attribute', 'a')
            ->where('av.id IN (:ids)')
            ->setParameter('ids', array_keys($attributeValueIds))
            ->addOrderBy('a.urlPriority')
            ->addOrderBy('av.urlPriority')
            ->getQuery()
            ->getResult();
        /* @var $attributeValues LightAttributeValue[] */

        $this->_em->getRepository('MetalAttributesBundle:AttributeValue')
            ->attachAttributesToLightAttributeValues($attributeValues);

        foreach ($attributeValues as $attributeValue) {
            $productsIds = $productsPerParameterOptions[$attributeValue->getId()];
            foreach ($productsIds as $productId) {
                $productToAttributesValue[$productId][] = $attributeValue;
            }
        }

        foreach ($products as $product) {
            $attributesCollection = new AttributesCollection();
            if (isset($productToAttributesValue[$product->getId()] )) {
                $attributesCollection->appendAttributeValues($productToAttributesValue[$product->getId()]);
            }
            $product->setAttribute('product_attributes_collection', $attributesCollection);
        }
    }
}
