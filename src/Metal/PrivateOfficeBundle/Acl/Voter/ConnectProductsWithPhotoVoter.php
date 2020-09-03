<?php

namespace Metal\PrivateOfficeBundle\Acl\Voter;

use Metal\ProductsBundle\Entity\ProductImage;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ConnectProductsWithPhotoVoter extends Voter
{
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        return $subject instanceof ProductImage && $attribute === 'CAN_CONNECT_PRODUCTS_WITH_PHOTO';
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /* @var $subject ProductImage */
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

        if (!$user->getHasEditPermission()) {
            return false;
        }

        if (!$user->getCompany()->getPackageChecker()->isAllowedConnectWithPhoto()) {
            return false;
        }

        return !$subject->getCompany() || $subject->getCompany()->getId() === $user->getCompany()->getId();
    }
}
