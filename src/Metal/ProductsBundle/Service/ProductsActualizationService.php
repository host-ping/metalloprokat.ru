<?php

namespace Metal\ProductsBundle\Service;

use Brouzie\Components\Indexer\Indexer;
use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Indexer\Operation\ProductChangeSet;
use Metal\ProductsBundle\Indexer\Operation\ProductsCriteria;
use Metal\ProductsBundle\Repository\ProductRepository;

class ProductsActualizationService
{
    private $em;

    private $indexer;

    public function __construct(EntityManager $em, Indexer $indexer)
    {
        $this->em = $em;
        $this->indexer = $indexer;
    }

    public function actualizeProducts(Company $company): bool
    {
        $companyCounter = $company->getCounter();
        $actualizedAt = $companyCounter->getProductsActualizedAt();

        // с момента последней актуализаци прошло меньше суток, нет смысла что-либо обновлять.
        if ($actualizedAt && $actualizedAt->diff(new \DateTime('tomorrow midnight -1 second'))->days < 1) {
            
            $companyCounter->setProductsUpdatedAt(new \DateTime());
            $companyCounter->setProductsActualizedAt(new \DateTime());
            $this->em->flush();

            return false;
        }

        /** @var  ProductRepository $productRepository */
        $productRepository = $this->em->getRepository('MetalProductsBundle:Product');

        $productRepository
            ->createQueryBuilder('p')
            ->update('MetalProductsBundle:Product', 'p')
            ->set('p.updatedAt', ':date')
            ->andWhere('p.company = :company_id')
            ->andWhere('p.checked NOT IN (:statuses)')
            ->setParameter('statuses', [Product::STATUS_DELETED, Product::STATUS_PENDING_CATEGORY_DETECTION])
            ->setParameter('company_id', $company->getId())
            ->setParameter('date', new \DateTime())
            ->getQuery()
            ->execute();

        $this->em->getRepository('MetalStatisticBundle:StatsProductChange')->updateProductChanges($company->getId());

        $changeSet = new ProductChangeSet();
        $changeSet->setUpdatedAt(new \DateTime('today midnight'));

        $criteria = new ProductsCriteria();
        $criteria->setCompanyId($company->getId());

        $this->indexer->update($changeSet, $criteria);

        $companyCounter->setProductsUpdatedAt(new \DateTime());
        $companyCounter->setProductsActualizedAt(new \DateTime());

        $this->em->flush();

        return true;
    }
}
