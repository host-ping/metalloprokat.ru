<?php

namespace Metal\DemandsBundle\Repository;

use Metal\DemandsBundle\Entity\PrivateDemand;

class PrivateDemandRepository extends AbstractDemandRepository
{
    public function getCitiesForDemands($company, $viewedFlag = null, $category = null, array $userTerritories = array())
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('IDENTITY(pd.city) AS cityId')
            ->from('MetalDemandsBundle:PrivateDemand', 'pd')
            ->andWhere('pd.company = :company')
            ->andWhere('pd.deletedAt IS NULL')
            ->setParameter('company', $company)
            ->groupBy('pd.city');

        if ($category) {
            $qb
                ->andWhere('pd.category = :category')
                ->setParameter('category', $category);
        }

        if ($viewedFlag) {
            $qb->andWhere('pd.viewedBy IS NOT NULL');
        } elseif ($viewedFlag === false) {
            $qb->andWhere('pd.viewedBy IS NULL');
        }

        $this->_em->getRepository('MetalCompaniesBundle:CompanyCity')->applyFilterByTerritory('pd', $qb, $userTerritories);

        $citiesIds = $qb->getQuery()->getResult();
        $citiesIds = array_column($citiesIds, 'cityId');

        return $this->_em->getRepository('MetalTerritorialBundle:City')->findBy(array('id' => $citiesIds), array('title' => 'ASC'));
    }

    public function getCategoriesForDemands($company, $viewedFlag = null, array $userTerritories = array())
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('IDENTITY(pd.category) AS categoryId')
            ->from('MetalDemandsBundle:PrivateDemand', 'pd')
            ->andWhere('pd.company = :company')
            ->andWhere('pd.category IS NOT NULL')
            ->andWhere('pd.deletedAt IS NULL')
            ->setParameter('company', $company)
            ->groupBy('pd.category');

        if ($viewedFlag) {
            $qb->andWhere('pd.viewedBy IS NOT NULL');
        } elseif ($viewedFlag === false) {
            $qb->andWhere('pd.viewedBy IS NULL');
        }

        $this->_em->getRepository('MetalCompaniesBundle:CompanyCity')->applyFilterByTerritory('pd', $qb, $userTerritories);

        $categoriesIds = $qb->getQuery()->getResult();
        $categoriesIds = array_column($categoriesIds, 'categoryId');

        return $this->_em->getRepository('MetalCategoriesBundle:Category')->findBy(array('id' => $categoriesIds), array('title' => 'ASC'));
    }

    /**
     * @param PrivateDemand[] $demands
     */
    public function attachProductsToPrivateDemands($demands)
    {
        if (!count($demands)) {
            return;
        }

        $products = array();
        foreach ($demands as $demand) {
            if ($demand->getProduct()) {
                $products[$demand->getProduct()->getId()] = true;
            }
        }

        if (!count($products)) {
            return;
        }

        $this->_em->getRepository('MetalProductsBundle:Product')->findBy(array('id' => array_keys($products)));
    }
}
