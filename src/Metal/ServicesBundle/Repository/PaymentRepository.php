<?php

namespace Metal\ServicesBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\ServicesBundle\Entity\Payment;
use Metal\UsersBundle\Entity\User;

class PaymentRepository extends EntityRepository
{
    public function getUnpaidPaymentsCount(User $user)
    {
        $company = $user->getCompany();

        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.status = :pending')
            ->andWhere('p.deletedAt IS NULL')
            ->setParameter('pending', Payment::STATUS_PENDING);

        if ($company) {
            $qb
                ->andWhere('p.company = :company')
                ->setParameter('company', $company);
        } else {
            $qb
                ->join('p.packageOrder', 'po')
                ->andWhere('po.user = :user')
                ->setParameter('user', $user);
        }

        return (int)$qb->getQuery()->getSingleScalarResult();
    }
}
