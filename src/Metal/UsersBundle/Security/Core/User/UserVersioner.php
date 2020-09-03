<?php

namespace Metal\UsersBundle\Security\Core\User;

use Brouzie\Bundle\CrossdomainAuthBundle\Security\Core\User\UserVersionerInterface;
use Doctrine\ORM\EntityManager;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVersioner implements UserVersionerInterface
{
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function incrementUserVersion(UserInterface $user)
    {
        /* @var $user User */

        $this->em
            ->createQuery('UPDATE MetalUsersBundle:User u SET u.userVersion = u.userVersion + 1 WHERE u.id = :id')
            ->setParameter('id', $user->getId())
            ->execute();
    }
}
