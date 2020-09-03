<?php

namespace Metal\UsersBundle\CrossdomainAuth;

use Brouzie\Bundle\CrossdomainAuthBundle\ResponseSigner\ResponseSignerInterface;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class ResponseSigner implements ResponseSignerInterface
{
    public function signUser(UserInterface $user, $secretKey)
    {
        /* @var $user User */
        $parts = array($user->getUsername(), $user->getPassword(), $user->getUserVersion(), $secretKey);

        return substr(sha1(implode('-', $parts)), 18, 16);
    }
}
