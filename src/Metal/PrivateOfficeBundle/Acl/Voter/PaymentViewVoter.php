<?php

namespace Metal\PrivateOfficeBundle\Acl\Voter;

use Metal\ServicesBundle\Entity\Payment;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class PaymentViewVoter extends Voter
{
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        return $subject instanceof Payment && $attribute === 'CAN_VIEW_PAYMENT';
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /* @var $subject Payment */
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        $roles = array(array('ROLE_SUPPLIER'), array('ROLE_CONFIRMED_EMAIL'));
        foreach ($roles as $role) {
            if (!$this->decisionManager->decide($token, $role)) {
                return false;
            }
        }

        return $user->getHasEditPermission() && $subject->getCompany()->getId() === $user->getCompany()->getId();
    }
}
