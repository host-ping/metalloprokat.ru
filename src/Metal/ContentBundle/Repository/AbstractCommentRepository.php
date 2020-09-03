<?php

namespace Metal\ContentBundle\Repository;

use Doctrine\ORM\QueryBuilder;
use Metal\ContentBundle\Entity\ValueObject\StatusTypeProvider;
use Metal\ProjectBundle\Doctrine\EntityRepository;

class AbstractCommentRepository extends EntityRepository
{
    /**
     * @param $object
     *
     * @return array
     */
    public function getCommentsByObject($object)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('c as comment')
            ->where('c.statusTypeId IN (:statuses)')
            ->leftJoin('c.user', 'u')
            ->addSelect('u.id as userId')
            //TODO: delete POTENTIAL_SPAM after moderation
            ->setParameter('statuses', array(StatusTypeProvider::NOT_CHECKED, StatusTypeProvider::CHECKED, StatusTypeProvider::POTENTIAL_SPAM));

        $this->filterQueryByObject($qb, $object);

        $comments = $qb
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return array_column($comments, 'comment');
    }

    protected function filterQueryByObject(QueryBuilder $qb, $object)
    {
        throw new \BadMethodCallException('You must implement this method in subclasses repository.');
    }
}
