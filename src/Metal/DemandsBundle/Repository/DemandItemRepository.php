<?php

namespace Metal\DemandsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\CategoriesBundle\Entity\Category;
use Metal\DemandsBundle\Entity\AbstractDemand;

class DemandItemRepository extends EntityRepository
{
    public function loadDemandItemsCollectionForCompany(AbstractDemand $demand)
    {
        if (!$demand->getId()) {
            return;
        }

        $demandsItems = $this->_em->createQueryBuilder()
            ->select('di')
            ->from('MetalDemandsBundle:DemandItem', 'di')
            ->leftJoin('di.category', 'c')
            ->addSelect('c')
            ->where('di.demand = :demand')
            ->setParameter('demand', $demand)
            ->getQuery()
            ->getResult();

        $coll = $demand->getDemandItems();
        /* @var $coll \Doctrine\ORM\PersistentCollection */

        foreach ($demandsItems as $demandCategory) {
            $coll->hydrateAdd($demandCategory);
        }
        $coll->setInitialized(true);
    }

    /**
     * @param AbstractDemand[] $demands
     * @param Category $preferredCategory
     */
    public function attachDemandItems(array $demands, Category $preferredCategory = null)
    {
        if (!count($demands)) {
            return;
        }

        $directedDemands = array();
        foreach ($demands as $demand) {
            $demand->setAttribute('demandItems', array());
            $directedDemands[$demand->getId()] = $demand;
        }

        $qb = $this->_em->createQueryBuilder()
            ->select('di AS demandItem, IDENTITY(di.demand) AS demandId')
            ->from('MetalDemandsBundle:DemandItem', 'di')
            ->leftJoin('di.category', 'c')
            ->addSelect('c')
            ->andWhere('di.demand IN (:demands_ids)')
            ->setParameter('demands_ids', array_keys($directedDemands));

        $demandsItems = $qb
            ->getQuery()
            ->getResult();

        //TODO: это можно решить и на уровне sql
        $currentCategoryDemandItem = array();
        if ($preferredCategory) {
            foreach ($demandsItems as $key => $demandItem) {
                $category = $demandItem['demandItem']->getCategory();
                if ($category && in_array($preferredCategory->getId(), $category->getBranchIds())) {
                    $currentCategoryDemandItem[] = $demandItem;
                    unset($demandsItems[$key]);
                }
            }

            $demandsItems = array_merge($currentCategoryDemandItem, $demandsItems);
        }

        $demandItemsPerDemand = array();
        foreach ($demandsItems as $demandsItem) {
            $id = $demandsItem['demandId'];
            if (!isset($demandItemsPerDemand[$id])) {
                $demandItemsPerDemand[$id] = array();
            }

            $demandItemsPerDemand[$id][] = $demandsItem['demandItem'];
        }

        foreach ($demandItemsPerDemand as $id => $demandItems) {
            $directedDemands[$id]
                ->setAttribute('demandItems', $demandItems);
        }
    }
}
