<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Metal\DemandsBundle\Entity\PrivateDemand;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PrivateDemandController extends Controller
{
    /**
     * @ParamConverter("privateDemand", class="MetalDemandsBundle:PrivateDemand")
     * @Security("has_role('ROLE_SUPPLIER') and (privateDemand.getCompany().getId() == user.getCompany().getId())")
     */
    public function showAction(PrivateDemand $privateDemand)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$privateDemand->getViewedBy()) {
            $user = $this->getUser();

            $privateDemand->setViewedAt(new \DateTime());
            $privateDemand->setViewedBy($user);

            $em->flush();
            $em->getRepository('MetalCompaniesBundle:CompanyCounter')->updateViewDemandCounter(array($privateDemand->getCompany()));
        }

        return new JsonResponse(array(
            'success' => true,
        ));
    }
}
