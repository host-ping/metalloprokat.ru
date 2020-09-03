<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\UsersBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction()
    {
        if (!$this->isGranted('ROLE_SUPPLIER')) {
            return $this->redirect($this->generateUrl('MetalPrivateOfficeBundle:Account:view'));
        }

        $companyStatistics = array(
            'reviewsProductsCount' => 0,
            'demandsCount' => 0,
            'callbacksCount' => 0,
        );

        $newCallbacksCount = $newDemandsCount = 0;
        if ($this->isGranted('ROLE_APPROVED_USER')) {

            $em = $this->getDoctrine()->getManager();
            /* @var $em EntityManager */

            $user = $this->getUser();
            /* @var $user User */

            $companyStatistics = $em->getRepository('MetalStatisticBundle:StatsDaily')
                ->getCompanyStatistics($user->getCompany());

            $companyCounterRepository = $em->getRepository('MetalCompaniesBundle:CompanyCounter');
            $newCallbacksCount = $companyCounterRepository->getNewCallbacksCountForUser($user);
            $newDemandsCount = $companyCounterRepository->getNewDemandsCountForUser($user);
        }

        return $this->render(
            '@MetalPrivateOffice/Homepage/homepage.html.twig',
            array(
                'companyStatistics' => $companyStatistics,
                'callbacksCount' => $newCallbacksCount,
                'demandsCount' => $newDemandsCount,
            )
        );
    }

    public function notApprovedAction(Request $request)
    {
        if ($this->isGranted('ROLE_APPROVED_USER')) {
            return $this->redirect($this->generateUrl('MetalPrivateOfficeBundle:Default:index'));
        }

        return $this->render(
            '@MetalPrivateOffice/Approved/not_approved.html.twig',
            array('redirect_url' => $request->query->get('redirect_url'))
        );
    }

    /**
     * @Security("has_role('ROLE_USER') and not has_role('ROLE_APPROVED_USER')")
     */
    public function sendReminderEmailAction()
    {
        $user = $this->getUser();
        /* @var $user User */

        $this->get('metal.users.users_mailer')->notifyMainUserOnRegistration($user);

        return JsonResponse::create(
            array(
                'status' => 'success',
            )
        );
    }
}
