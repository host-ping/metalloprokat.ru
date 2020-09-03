<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CallbacksBundle\Entity\Callback;
use Metal\UsersBundle\Entity\User;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class PrivateCallbackController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUPPLIER')")
     */
    public function listAction(Request $request)
    {
        $user = $this->getUser();
        /* @var $user User */

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $callbackRepository = $em->getRepository('MetalCallbacksBundle:Callback');
        $companyCityRepository = $em->getRepository('MetalCompaniesBundle:CompanyCity');

        $company = $user->getCompany();
        $callbacksQb = $callbackRepository
            ->createQueryBuilder('c')
            ->andWhere('c.company = :company')
            ->andWhere('c.kind = :to_company')
            ->orderBy('c.createdAt', 'DESC')
            ->setParameter('company', $company)
            ->setParameter('to_company', Callback::CALLBACK_TO_SUPPLIER);

        if ($user->requireApplyTerritorialRules()) {
            $citiesCriteria = $companyCityRepository->getCitiesCriteriaForUser($user);
            $companyCityRepository->applyFilterByTerritory('c', $callbacksQb, $citiesCriteria);
        }

        if ($request->query->get('notReturnCalls')) {
            $callbacksQb->andWhere('c.processedBy IS NULL');
        }

        $perPage = 20;

        $pagerfanta = (new Pagerfanta(new DoctrineORMAdapter($callbacksQb, false)))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($request->query->get('page', 1));


        if ($request->isXmlHttpRequest()) {
            return $this->render(
                '@MetalPrivateOffice/partials/callbacks_list.html.twig',
                array(
                    'pagerfanta' => $pagerfanta
                )
            );
        }

        return $this->render(
            '@MetalPrivateOffice/PrivateCallback/list.html.twig',
            array(
                'pagerfanta' => $pagerfanta
            )
        );
    }

    /**
     * @ParamConverter("callback", class="MetalCallbacksBundle:Callback")
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL') and (callback.getCompany().getId() == user.getCompany().getId())")
     */
    public function processCallbackAction(Callback $callback)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $callback->setProcessedAt(new \DateTime());
        $callback->setProcessedBy($this->getUser());

        $em->flush();

        $em->getRepository('MetalCompaniesBundle:CompanyCounter')
            ->updateCompaniesNewCallbacksCount(array($callback->getCompany()->getId()));

        return JsonResponse::create(array(
            'status' => 'success',
            'date' => $this->get('brouzie.helper_factory')
                ->get('MetalProjectBundle:Formatting')
                ->formatDateTime($callback->getProcessedAt())
        ));
    }
}
