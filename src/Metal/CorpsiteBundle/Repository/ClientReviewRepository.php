<?php

namespace Metal\CorpsiteBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ClientReviewRepository extends EntityRepository
{
    public function getRandomClientReview()
    {
        $reviews = $this->getClientReviews();

        if (!count($reviews)) {
            return null;
        }

        return $reviews[mt_rand(0, count($reviews) - 1)];
    }

    public function getClientReviews()
    {
        $reviews = $this
            ->createQueryBuilder('cr')
            ->join('cr.company', 'c')
            ->addSelect('c')
            ->where('cr.moderatedAt IS NOT NULL')
            ->andWhere('cr.deletedAt IS NULL')
            ->addOrderBy('c.codeAccess', 'DESC')
            ->getQuery()
            ->getResult();

        return $reviews;
    }
}
