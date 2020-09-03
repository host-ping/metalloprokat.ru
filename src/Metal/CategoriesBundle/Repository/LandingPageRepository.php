<?php

namespace Metal\CategoriesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\TerritorialBundle\Entity\City;

class LandingPageRepository extends EntityRepository
{
    /**
     * @param int $country
     * @param array $orderBy key - column, value - direction
     *
     * @return City[]
     */
    public function getLandingCitiesWithSlug($country, array $orderBy = array(), $limit = null)
    {
        $citiesQb = $this->_em
            ->createQueryBuilder()
            ->select('c')
            ->from('MetalTerritorialBundle:City', 'c', 'c.id')
            ->join('MetalCategoriesBundle:LandingPage', 'lp', 'WITH', 'lp.city = c.id')
            ->groupBy('c.id')
            ->andWhere('c.slug IS NOT NULL')
            ->andWhere('c.country = :country')
            ->setParameter('country', $country);

        foreach ($orderBy as $order => $dir) {
            $citiesQb->addOrderBy('c.'.$order, $dir);
        }

        return $citiesQb
            ->getQuery()
            ->setMaxResults($limit)
            ->getResult();
    }

    public function resetCounters($landingPages = array())
    {
        $qb = $this->createQueryBuilder('lp')
            ->update('MetalCategoriesBundle:LandingPage', 'lp')
            ->set('lp.resultsCount', 0)
        ;

        if ($landingPages) {
            $qb
                ->andWhere('lp.id IN (:landing_pages)')
                ->setParameter('landing_pages', $landingPages);
        }

        $qb
            ->getQuery()
            ->execute();
    }
}
