<?php

namespace Metal\CatalogBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\CatalogBundle\Entity\Product;

class ProductAttributeValueRepository extends EntityRepository
{
    /**
     * @param Product[] $products
     */
    public function attachAttributesForProducts($products)
    {
        foreach ($products as $product) {
            $product->setAttribute('product_attributes', array());
        }

        $attributes = $this->loadAttributesForProducts($products);

        foreach ($products as $product) {
            $product->setAttribute(
                'product_attributes',
                isset($attributes[$product->getId()]) ? $attributes[$product->getId()] : array()
            );
        }
    }

    /**
     * @param Product[] $products
     *
     * @return array
     */
    public function loadAttributesForProducts(array $products)
    {
        $attrValueIds = array();
        foreach ($products as $product) {
            $attrValueIds[] = $product->getManufacturer()->getId();
            $attrValueIds[] = $product->getBrand()->getId();
        }

        $this->_em->getRepository('MetalAttributesBundle:AttributeValue')->findBy(array('id' => $attrValueIds));

        $attributesPerProduct = array();
        foreach ($products as $product) {
            $attributesPerProduct[$product->getId()][] =
                array(
                    'attributeValueTitle' => $product->getManufacturer()->getValue(),
                    'attributeTitle' => $product->getManufacturer()->getAttribute()->getTitle(),
                    'code' => Product::ATTR_CODE_MANUFACTURER,
                );

            $attributesPerProduct[$product->getId()][] =
                array(
                    'attributeValueTitle' => $product->getBrand()->getValue(),
                    'attributeTitle' => $product->getBrand()->getAttribute()->getTitle(),
                    'code' => Product::ATTR_CODE_BRAND,
                );
        }

        $attributes = $this->_em
            ->createQueryBuilder()
            ->from('MetalCatalogBundle:ProductAttributeValue', 'pav')
            ->join('pav.attributeValue', 'av')
            ->join('av.attribute', 'a')
            ->andWhere('a.code NOT IN(:notAttributesIds)')
            ->setParameter('notAttributesIds', array(Product::ATTR_CODE_BRAND, Product::ATTR_CODE_MANUFACTURER))
            ->addSelect('IDENTITY(pav.product) AS productId')
            ->addSelect('av.value AS attributeValueTitle')
            ->addSelect('a.title AS attributeTitle')
            ->addSelect('a.code AS code')
            ->addOrderBy('a.outputPriority')
            ->addOrderBy('av.outputPriority')
            ->andWhere('pav.product IN (:products)')
            ->setParameter('products', $products)
            ->getQuery()
            ->getArrayResult();

        foreach ($attributes as $attribute) {
            $attributesPerProduct[$attribute['productId']][] = $attribute;
        }

        return $attributesPerProduct;
    }

    /**
     * @param array $productsIds
     *
     * @return array
     */
    public function getProductsAttributes(array $productsIds = array())
    {
        if (!$productsIds) {
            return array();
        }

        $productToAttributes = array();
        foreach ($productsIds as $productId) {
            $productToAttributes[$productId] = array();
        }

        $productsAttributes = $this->_em->createQueryBuilder()
            ->from('MetalCatalogBundle:ProductAttributeValue', 'productAttributeValue')
            ->join('productAttributeValue.attributeValue', 'attributeValue')
            ->select('attributeValue.id AS attribute_value_id')
            ->addSelect('attributeValue.value AS attribute_value_title')
            ->addSelect('IDENTITY(productAttributeValue.product) AS product_id')
            ->addSelect('IDENTITY(attributeValue.attribute) AS attribute_id')
            ->where('productAttributeValue.product IN (:products_ids)')
            ->setParameter('products_ids', $productsIds)
            ->getQuery()
            ->getResult();

        foreach ($productsAttributes as $productsAttribute) {
            $productToAttributes[$productsAttribute['product_id']][$productsAttribute['attribute_id']] = array(
                'attribute_id' => $productsAttribute['attribute_id'],
                'attribute_value_id' => $productsAttribute['attribute_value_id'],
                'attribute_value_title' => $productsAttribute['attribute_value_title'],
            );
        }

        return $productToAttributes;
    }
}
