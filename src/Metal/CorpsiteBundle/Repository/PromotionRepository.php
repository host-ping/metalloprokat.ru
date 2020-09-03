<?php

namespace Metal\CorpsiteBundle\Repository;

use Doctrine\ORM\EntityRepository;

class PromotionRepository extends EntityRepository
{
    public function getActivePromotions()
    {
        return $this
            ->createQueryBuilder('p')
            ->where(':now BETWEEN p.startsAt AND p.endsAt')
            ->setParameter('now', new \DateTime())
            ->orderBy('p.startsAt', 'ASC')
            ->getQuery()
            ->getResult();

    }
}
