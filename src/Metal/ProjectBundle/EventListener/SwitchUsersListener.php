<?php

namespace Metal\ProjectBundle\EventListener;

use Metal\UsersBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Event\SwitchUserEvent;

class SwitchUsersListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onSwitchUser(SwitchUserEvent $event)
    {
        $targetUser = $event->getTargetUser();

        if ($this->authorizationChecker->isGranted('ROLE_PREVIOUS_ADMIN')) {
            // _switch_user=_exit - Возвращаемся к прошлому пользователю
            return;
        }

        $currentUser = $this->tokenStorage->getToken()->getUser();

        if ($currentUser->getAdditionalRoleId() != User::ROLE_SUPER_ADMIN && $targetUser->getAdditionalRoleId() > User::ROLE_DEFAULT) {
            throw new AccessDeniedException('У вас нет прав входить под данным пользователем.');
        }
    }
}
