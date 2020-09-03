<?php

namespace Metal\DemandsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\DemandsBundle\Entity\Demand;
use Metal\DemandsBundle\Entity\DemandCategory;

class DemandCategoryRepository extends EntityRepository
{
    /**
     * @param Demand[] $demands
     * @return null
     */
    public function attachToDemands(array $demands)
    {
        if (!$demands) {
            return null;
        }

        $demandsByIds = array();
        foreach ($demands as $demand) {
            $demandsByIds[$demand->getId()] = $demand;
        }
        /* @var $demandsByIds Demand[] */

        $demandsCategories = $this->_em->getRepository('MetalDemandsBundle:DemandCategory')
            ->createQueryBuilder('demandCategory')
            ->select('demandCategory')
            ->addSelect('category')
            ->join('demandCategory.category', 'category')
            ->where('demandCategory.demand IN(:demandsIds)')
            ->setParameter('demandsIds', array_keys($demandsByIds))
            ->getQuery()
            ->getResult()
        ;
        /* @var $demandsCategories DemandCategory[] */
        //TODO: Проверить логику с предыдущими комитами
        foreach ($demandsCategories as $demandCategory) {
            $demand = $demandsByIds[$demandCategory->getDemand()->getId()];
            if ($demand->hasAttribute('categories')) {
                continue;
            }

            $demand->setAttribute('categories', array($demandCategory->getCategory()));
        }
    }
}
