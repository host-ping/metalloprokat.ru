<?php

namespace Metal\DemandsBundle\Repository;

use Doctrine\ORM\EntityRepository;

class DemandSubscriptionCategoryRepository extends EntityRepository
{
    public function getCategoryIdsPerUser(array $usersIds)
    {
        $rows = $this->getSubscribedCategoryIds($usersIds);
        $categoriesPerUser = array_fill_keys($usersIds, []);

        foreach ($rows as $row) {
            $userId = $row['userId'];
            $categoriesPerUser[$userId][] = $row['categoryId'];
        }

        return $categoriesPerUser;
    }

    /**
     * @param int[] $usersIds
     *
     * @return array[]
     */
    private function getSubscribedCategoryIds(array $usersIds): array
    {
        return $this
            ->createQueryBuilder('ds')
            ->select('IDENTITY(ds.user) AS userId')
            ->addSelect('IDENTITY(ds.category) AS categoryId')
            ->andWhere('ds.user IN (:user_ids)')
            ->setParameter('user_ids', $usersIds)
            ->getQuery()
            ->getResult();
    }
}
