<?php

namespace Metal\SupportBundle\Repository;

use Doctrine\ORM\EntityRepository;

class AnswerRepository extends EntityRepository
{
    public function markAnswersAsViewed(array $answers)
    {
        $this->_em
            ->createQueryBuilder()
            ->update($this->_entityName, 'a')
            ->set('a.viewedAt', ':now')
            ->setParameter('now', new \DateTime())
            ->where('a.id IN (:answers)')
            ->setParameter('answers', $answers)
            ->andWhere('a.viewedAt IS NULL')
            ->getQuery()
            ->execute()
        ;
    }
}
