<?php

namespace Metal\CompaniesBundle\Acl\Voter;

use Metal\CompaniesBundle\Entity\Company;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CompanyVoter extends Voter
{
    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        return $subject instanceof Company && $attribute === 'COMPANY_MODERATOR';
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if (!$this->decisionManager->decide($token, array('ROLE_SUPPLIER'))) {
            return false;
        }

        return $token->getUser()->getCompany()->getId() == $subject->getId();
    }
}
