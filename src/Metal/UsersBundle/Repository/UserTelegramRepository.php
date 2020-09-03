<?php

namespace Metal\UsersBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserTelegramRepository extends EntityRepository
{
    public function getTelegramUserIdsPerUserId(): array
    {
        $rows = $this
            ->createQueryBuilder('ut')
            ->select('IDENTITY(ut.user) AS userId')
            ->addSelect('ut.telegramUserId')
            ->getQuery()
            ->getResult();

        return array_column($rows, 'telegramUserId', 'userId');
    }
}
