<?php

namespace Metal\ContentBundle\Repository;

use Doctrine\ORM\QueryBuilder;

class InstagramCommentRepository extends AbstractCommentRepository
{
    protected function filterQueryByObject(QueryBuilder $qb, $object)
    {
        $qb
            ->andWhere('c.instagramPhoto = :photo')
            ->setParameter('photo', $object);
    }
}