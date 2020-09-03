<?php

namespace Metal\PrivateOfficeBundle\Acl\Voter;

use Metal\SupportBundle\Entity\Topic;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class SupportTopicVoter extends Voter
{
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        return $subject instanceof Topic && $attribute === 'CAN_VIEW_OR_ANSWER_TOPIC';
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /* @var $subject Topic */
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        $roles = array(array('ROLE_USER'));
        foreach ($roles as $role) {
            if (!$this->decisionManager->decide($token, $role)) {
                return false;
            }
        }

        if ($subject->getAuthor()->getId() == $user->getId()) {
            return true;
        }

        return $subject->getCompany() && $user->getCompany() && $subject->getCompany()->getId() == $user->getCompany()->getId();
    }
}
