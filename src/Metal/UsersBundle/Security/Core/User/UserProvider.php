<?php

namespace Metal\UsersBundle\Security\Core\User;

use Doctrine\ORM\EntityManager;
use Jmikola\AutoLogin\Exception\AutoLoginTokenNotFoundException;
use Jmikola\AutoLogin\User\AutoLoginUserProviderInterface;
use Metal\NewsletterBundle\Service\SubscriberService;
use Metal\UsersBundle\Entity\UserAutoLogin;

class UserProvider implements AutoLoginUserProviderInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var SubscriberService
     */
    protected $subscriberService;

    public function __construct(EntityManager $em, SubscriberService $subscriberService)
    {
        $this->em = $em;
        $this->subscriberService = $subscriberService;
    }

    public function loadUserByAutoLoginToken($key)
    {
        $userAutoLogin = $this->em->getRepository('MetalUsersBundle:UserAutoLogin')->findOneBy(array('token' => $key));
        /* @var $userAutoLogin UserAutoLogin */

        if ($userAutoLogin && $userAutoLogin->isAlive()) {
            $userAutoLogin->setAuthenticationsCount($userAutoLogin->getAuthenticationsCount() + 1);
            $user = $userAutoLogin->getUser();
            if (!$user->getIsEmailConfirmed()) {
                $user->setIsEmailConfirmed(true);
                $user->setRegistrationCode('');

                $this->subscriberService->removeUnnecessarySubscriberForUser($user);
                $this->subscriberService->createOrUpdateSubscriberForUser($user);
            }

            $this->em->flush();

            return $userAutoLogin->getUser();
        }

        throw new AutoLoginTokenNotFoundException();
    }
}
