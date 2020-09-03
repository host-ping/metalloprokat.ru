<?php

namespace Metal\DemandsBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\ProjectBundle\Doctrine\EntityRepository;

class AbstractDemandRepository extends EntityRepository
{
    /**
     * @param AbstractDemand[] $demands
     * @param $user
     */
    public function attachDemandIsInFavorite(array $demands, $user)
    {
        if (!count($demands) || !$user) {
            return;
        }

        $directedDemands = array();
        foreach ($demands as $demand) {
            $directedDemands[$demand->getId()] = $demand;
            $demand->setAttribute('isInFavorite', false);
        }

        $qb = $this->_em->createQueryBuilder()
            ->from('MetalUsersBundle:Favorite', 'f')
            ->select('IDENTITY(f.demand) AS demandId')
            ->andWhere('f.user = :user')
            ->andWhere('f.demand IN (:demands_ids)')
            ->setParameter('user', $user)
            ->setParameter("demands_ids", array_keys($directedDemands));

        $favoriteDemands = $qb
            ->getQuery()
            ->getArrayResult();

        foreach ($favoriteDemands as $favoriteDemand) {
            $directedDemands[$favoriteDemand['demandId']]
                ->setAttribute('isInFavorite', true);
        }
    }

    public function getDemandsQbBySpecification(array $specification, array $orderBy = array())
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('d')
            ->from($this->_entityName, 'd', !empty($specification['index_by_id']) ? 'd.id' : null);
        unset($specification['index_by_id']);

        $this->applySpecificationToQueryBuilder($qb, $specification);
        $this->applyOrderByToQueryBuilder($qb, $orderBy);

        return $qb;
    }

    protected function applySpecificationToQueryBuilder(QueryBuilder $qb, array $specification)
    {
        if (isset($specification['id'])) {
            $qb->andWhere('d.id IN (:id)')
                ->setParameter('id', $specification['id']);
        }

        if (isset($specification['preload_administrative_center'])) {
            $qb->leftJoin('d.city', 'c')
                ->leftJoin('c.administrativeCenter', 'adc')
                ->addSelect('c, adc');
        }
    }

    protected function applyOrderByToQueryBuilder(QueryBuilder $qb, array $orderBy)
    {
    }
}
