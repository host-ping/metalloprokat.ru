<?php

namespace Metal\DemandsBundle\Repository;

use Doctrine\ORM\EntityRepository;

class DemandSubscriptionTerritorialRepository extends EntityRepository
{
    public function getSubscribedTerritorialIdsPerUser(array $usersIds)
    {
        $rows = $this->getDemandSubscriptions($usersIds);
        $territorialPerUser = array_fill_keys($usersIds, []);

        foreach ($rows as $row) {
            $userId = $row['userId'];
            $territorialPerUser[$userId][] = $row['territorialStructureId'];
        }

        return $territorialPerUser;
    }

    /**
     * @param int[] $usersIds
     *
     * @return array[]
     */
    private function getDemandSubscriptions(array $usersIds): array
    {
        return $this
            ->createQueryBuilder('dst')
            ->select('IDENTITY(dst.territorialStructure) AS territorialStructureId, IDENTITY(dst.user) AS userId')
            ->andWhere('dst.user IN (:users_ids)')
            ->setParameter('users_ids', $usersIds)
            ->getQuery()
            ->getResult();
    }
}
