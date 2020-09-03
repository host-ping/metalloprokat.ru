<?php

namespace Metal\ProductsBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProductImageRepository extends EntityRepository
{
    public function getProductsImages($company, $name = '', $onlyCompany = false)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('productImage')
            ->from('MetalProductsBundle:ProductImage', 'productImage');

        if ($onlyCompany) {
            $qb->where('productImage.company = :company');
        } else {
            $qb->where('(productImage.company = :company or productImage.company IS NULL)');
        }

        $qb->setParameter('company', $company);

        $qb->andWhere('(productImage.url IS NULL or productImage.downloaded = true)');

        if (strlen($name) > 0) {
            $qb->andWhere('productImage.description LIKE :q')
                ->setParameter('q', '%'.$name.'%');
        }

        $qb->orderBy('productImage.createdAt', 'DESC');

        return $qb;
    }
}
