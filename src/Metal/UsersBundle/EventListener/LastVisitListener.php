<?php

namespace Metal\UsersBundle\EventListener;

use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Service\OnlineTracker;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class LastVisitListener
{
    private $tracker;

    private $logAdmins = true;

    private $tokenStorage;

    private $authorizationChecker;

    public function __construct(
        OnlineTracker $tracker,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->tracker = $tracker;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        // do not process forwards
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($this->tokenStorage->getToken() && $this->authorizationChecker->isGranted('ROLE_USER')
            && ($this->logAdmins || !$this->authorizationChecker->isGranted('ROLE_PREVIOUS_ADMIN'))) {

            /** @var User $user */
            $user = $this->tokenStorage->getToken()->getUser();
            $this->tracker->trackUserOnline($user, $event->getRequest()->getClientIp());
        }
    }
}
