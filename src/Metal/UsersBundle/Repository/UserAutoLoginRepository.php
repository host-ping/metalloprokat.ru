<?php

namespace Metal\UsersBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Entity\UserAutoLogin;

class UserAutoLoginRepository extends EntityRepository
{
    public function findAliveAutoLogin($user, $target)
    {
        return $this
            ->createQueryBuilder('ual')
            ->select('ual')
            ->where('ual.user = :user')
            ->andWhere('ual.target = :target')
            ->andWhere('ual.authenticationsCount <= :max_count')
            ->andWhere('ual.createdAt > :date_from')
            ->setParameter('user', $user)
            ->setParameter('target', $target)
            ->setParameter('max_count', UserAutoLogin::MAX_AUTHENTICATION_COUNT)
            ->setParameter('date_from', new \DateTime('-7 days'))
            ->setMaxResults(1)
            ->orderBy('ual.createdAt', 'DESC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function createUserAutoLogin(User $user, $target)
    {
        $userAutoLogin = new UserAutoLogin();
        $userAutoLogin->setUser($user);
        $userAutoLogin->setTarget($target);

        $this->_em->persist($userAutoLogin);
        $this->_em->flush($userAutoLogin);

        return $userAutoLogin;
    }

    public function findOrCreateAutoLogin(User $user, $target)
    {
        $userAutoLogin = $this->findAliveAutoLogin($user, $target);

        if (!$userAutoLogin) {
            $userAutoLogin = $this->createUserAutoLogin($user, $target);
        }

        return $userAutoLogin;
    }
}
