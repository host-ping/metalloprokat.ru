<?php

namespace Metal\DemandsBundle\Acl\Voter;

use Doctrine\ORM\EntityManagerInterface;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\DemandsBundle\Entity\DemandView;
use Metal\DemandsBundle\Entity\PrivateDemand;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DemandAnswerVoter extends Voter
{
    private $decisionManager;

    private $demandViewRepository;

    /**
     * @var array
     *
     * key - demand.id
     * value - true
     */
    private $viewedDemands;

    public function __construct(AccessDecisionManagerInterface $decisionManager, EntityManagerInterface $em)
    {
        $this->decisionManager = $decisionManager;
        $this->demandViewRepository = $em->getRepository('MetalDemandsBundle:DemandView');
    }

    protected function supports($attribute, $subject)
    {
        return $subject instanceof AbstractDemand && ($attribute === 'DEMAND_ANSWER' || $attribute === 'VIEW_DEMAND_CONTACTS');
    }

    public function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /* @var $subject AbstractDemand */
        if ($subject->isOpened()) {
            return true;
        }

        $user = $token->getUser();
        /* @var $user User */

        if (!$this->decisionManager->decide($token, array('ROLE_SUPPLIER'))) {
            return false;
        }

        if ($subject instanceof PrivateDemand) {
            return $user->getCompany()->getId() == $subject->getCompany()->getId();
        }

        if ($this->decisionManager->decide($token, array('ROLE_ALLOWED_VIEW_DEMAND_CONTACTS'))) {
            return true;
        }

        if ($user->getCompany()->getPromocode()) {
            if (null === $this->viewedDemands) {
                $this->viewedDemands = $this->demandViewRepository->getViewedDemandsForPromocode($user->getCompany());
            }

            if (isset($this->viewedDemands[$subject->getId()]) ||
                count($this->viewedDemands) < DemandView::MAX_CONTACTS_VIEW_COUNT_FOR_PROMOCODE
            ) {
                $this->viewedDemands[$subject->getId()] = true;

                return true;
            }
        }

        return false;
    }
}
