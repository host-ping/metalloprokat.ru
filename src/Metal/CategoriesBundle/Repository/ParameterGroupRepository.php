<?php

namespace Metal\CategoriesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\ProductsBundle\Entity\Product;

class ParameterGroupRepository extends EntityRepository
{
    /**
     * @param Product[] $products
     */
    public function attachAttributesForProducts($products)
    {
        $attributes = $this->loadAttributesForProducts($products);
        foreach ($products as $product) {
            $product->setAttribute('attributes', $attributes[$product->getId()]);
        }
    }

    /**
     * @param Product[] $products
     * @return array
     */
    public function loadAttributesForProducts(array $products)
    {
        $productParameterValues = $this->_em
            ->getRepository('MetalProductsBundle:ProductParameterValue')
            ->createQueryBuilder('ppv')
            ->select('IDENTITY(ppv.product) as productId, IDENTITY(ppv.parameterOption) as parameterOptionId')
            ->andWhere('ppv.product IN (:products)')
            ->setParameter('products', $products)
            ->getQuery()
            ->getResult();

        $usedParametersOptions = array();
        foreach ($productParameterValues as $row) {
            $usedParametersOptions[$row['parameterOptionId']] = true;
        }

        $params = $this->_em
            ->createQueryBuilder()
            ->from('MetalCategoriesBundle:Parameter', 'p')
            ->join('p.parameterOption', 'po')
            ->join('p.parameterGroup', 'pg')
            ->select('p')
            ->addSelect('po')
            ->addSelect('pg')
            ->addOrderBy('pg.priority')
            ->addOrderBy('po.title')
            ->andWhere('p.parameterOption IN (:paramsOptions)')
            ->setParameter('paramsOptions', array_keys($usedParametersOptions))
            ->getQuery()
            ->getArrayResult();

        $productsParams = array();
        foreach ($params as $param) {
            $productsParams[$param['parameterOption']['id']] = $param;
        }

        $parameters = array();
        foreach ($products as $product) {
            $parameters[$product->getId()] = array();
        }

        foreach ($productParameterValues as $row) {
            $parameters[$row['productId']][] = $productsParams[$row['parameterOptionId']];
        }

        return $parameters;
    }

    public function attachAttributesForProduct(Product $product)
    {
        $productParameterValues = $this->_em
            ->createQueryBuilder()
            ->from('MetalProductsBundle:ProductParameterValue', 'ppv')
            ->join('ppv.parameterOption', 'po')
            ->join('po.type', 'pt')
            ->select('ppv')
            ->addSelect('po')
            ->addSelect('pt')
            ->addOrderBy('pt.priority')
            ->andWhere('ppv.product = :product')
            ->setParameter('product', $product)
            ->getQuery()
            ->getArrayResult();

        $product->setAttribute('attributes', $productParameterValues);
    }
}
