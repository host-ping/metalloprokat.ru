<?php

namespace Metal\DemandsBundle\Repository;

use Metal\DemandsBundle\Entity\Demand;
use Metal\DemandsBundle\Entity\PrivateDemand;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;

class DemandRepository extends AbstractDemandRepository
{
    public function loadDemand($id)
    {
        $demand = $this->find($id);
        /* @var $demand Demand */

        if (!$demand || !$demand->isModerated() || $demand->isDeleted()) {
            return null;
        }

        return $demand;
    }

    /**
     * @param PrivateDemand[] $demands
     */
    public function attachCompaniesToDemands(array $demands)
    {
        $companiesIds = array();
        foreach ($demands as $demand) {
            if ($demand->getCompany()) {
                $companiesIds[$demand->getCompany()->getId()] = true;
            }
        }

        if (!$companiesIds) {
            return;
        }

        $this->_em->getRepository('MetalCompaniesBundle:Company')->findBy(array('id' => array_keys($companiesIds)));
    }

    /**
     * @param Demand[] $demands
     */
    public function attachCategoriesToDemands(array $demands)
    {
        $categoriesIds = array();
        foreach ($demands as $demand) {
            if ($demand->getCategory()) {
                $categoriesIds[$demand->getCategory()->getId()] = true;
            }
        }

        if (!$categoriesIds) {
            return;
        }

        $categoryRepository = $this->_em->getRepository('MetalCategoriesBundle:Category');
        $categories = $categoryRepository->findBy(array('id' => array_keys($categoriesIds)));
        $parentIds = array();
        foreach ($categories as $category) {
            if ($category->getParent()) {
                $parentIds[$category->getParent()->getId()] = true;
            }
        }

        if (!$parentIds) {
            return;
        }

        $categoryRepository->findBy(array('id' => array_keys($parentIds)));
    }

    /**
     * @param Demand $demand
     */
    public function attachIdDemandFromMetalloprokat(Demand $demand)
    {
        $demandIdFRomMetalloprokat = (int)$this->_em->getRepository('MetalDemandsBundle:DemandMirror')
            ->createQueryBuilder('md')
            ->select('md.originalDemandId')
            ->where('md.demand = :demand_id')
            ->setParameter('demand_id', $demand)
            ->getQuery()
            ->getSingleScalarResult();

        $demand->setAttribute('id_demand_from_metalloprokat', $demandIdFRomMetalloprokat);
    }

    /**
     * @param Demand[] $demands
     * @param boolean $loadRegion
     */
    public function attachCitiesToDemands(array $demands, $loadRegion = false)
    {
        if (!count($demands)) {
            return;
        }

        $citiesIds = array();
        foreach ($demands as $demand) {
            if ($demand->getCity()) {
                $citiesIds[$demand->getCity()->getId()] = true;
            }
        }

        if (!$citiesIds) {
            return;
        }

        $qb = $this->_em->getRepository('MetalTerritorialBundle:City')
            ->createQueryBuilder('c')
            ->andWhere('c.id IN (:cities_ids)')
            ->setParameter('cities_ids', array_keys($citiesIds));

        if ($loadRegion) {
            $qb
                ->addSelect('region')
                ->join('c.region', 'region');
        }

        $qb->getQuery()->getResult();
    }

    /**
     * @param FacetResultExtractor $facetResultExtractor
     *
     * @return Demand[]
     */
    public function loadByFacetResult(FacetResultExtractor $facetResultExtractor)
    {
        return $this->findByIds($facetResultExtractor->getIds(), true);
    }
}
