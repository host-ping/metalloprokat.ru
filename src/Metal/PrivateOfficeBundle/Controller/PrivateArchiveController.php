<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CallbacksBundle\Entity\Callback;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\DemandsBundle\Entity\PrivateDemand;
use Metal\UsersBundle\Entity\User;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PrivateArchiveController extends Controller
{
    /**
     * @ParamConverter("demand", class="MetalDemandsBundle:AbstractDemand", )
     * @Security("has_role('ROLE_USER')")
     */
    public function toggleDemandAction(AbstractDemand $demand, $subject)
    {
        switch ($subject) {
            case 'delete':
                $demand->setDeleted();
                break;
            case 'restore':
                $demand->setDeleted(false);
                break;
            case 'update' :
                $demand->setUpdatedAt(new \DateTime());
                break;
        }

        $demand->setUpdatedBy($this->getUser());
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $em->flush();

        if (!$demand->isPublic()) {
            $em->getRepository('MetalDemandsBundle:PrivateDemand')->attachProductsToPrivateDemands(array($demand));
        }

        $em->getRepository('MetalDemandsBundle:DemandItem')->attachDemandItems(array($demand));
        $em->getRepository('MetalDemandsBundle:DemandFile')->attachDemandFiles(array($demand));
        $em->getRepository('MetalDemandsBundle:DemandAnswer')->attachDemandAnswers(array($demand));

        return JsonResponse::create(
            array(
                'status' => 'success',
                'html' => $this->renderView(
                    '@MetalPrivateOffice/partials/archive_demand.html.twig',
                    array(
                        'demand' => $demand,
                    )
                ),
                'subject' => $subject
            )
        );
    }


    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function demandsAction(Request $request)
    {
        $user = $this->getUser();
        /* @var $user User */

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $archiveDemandsRepository = $em->getRepository('MetalDemandsBundle:AbstractDemand');
        $privateDemandsRepository = $em->getRepository('MetalDemandsBundle:PrivateDemand');
        $demandsRepository = $em->getRepository('MetalDemandsBundle:Demand');

        $archiveDemandsQb = $archiveDemandsRepository->createQueryBuilder('ad')
            ->andWhere('ad.user = :user')
            ->andWhere('ad.deletedAt IS NULL')
            ->orderBy('ad.createdAt', 'DESC')
            ->setParameter('user', $user);

        $perPage = 20;
        $queryBag = $request->query;
        $pagerfanta = (new Pagerfanta(new DoctrineORMAdapter($archiveDemandsQb, false)))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($queryBag->get('page', 1));

        $archiveDemands = iterator_to_array($pagerfanta->getIterator());

        $privateDemands = array();
        foreach ($archiveDemands as $demand) {
            if ($demand instanceof PrivateDemand ) {
                $privateDemands[] = $demand;
            }
        }

        $privateDemandsRepository->attachProductsToPrivateDemands($privateDemands);
        $demandsRepository->attachCitiesToDemands($privateDemands, true);

        $demandItemRepository = $em->getRepository('MetalDemandsBundle:DemandItem');
        $demandItemRepository->attachDemandItems($archiveDemands);

        $demandFileRepository = $em->getRepository('MetalDemandsBundle:DemandFile');
        $demandFileRepository->attachDemandFiles($archiveDemands);

        $demandAnswerRepository = $em->getRepository('MetalDemandsBundle:DemandAnswer');
        $demandAnswerRepository->attachDemandAnswers($archiveDemands);

        if ($request->isXmlHttpRequest()) {
            return $this->render('@MetalPrivateOffice/partials/archive_demands_list.html.twig', array(
                'pagerfanta' => $pagerfanta
            ));
        }

        return $this->render('@MetalPrivateOffice/PrivateArchive/demands.html.twig', array(
            'pagerfanta' => $pagerfanta
        ));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function callbacksAction(Request $request)
    {
        $user = $this->getUser();
        /* @var $user User */

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $callbackRepository = $em->getRepository('MetalCallbacksBundle:Callback');

        $callbacksQb = $callbackRepository
            ->createQueryBuilder('c')
            ->andWhere('c.user = :user')
            ->orderBy('c.createdAt', 'DESC')
            ->setParameter('user', $user);

        $perPage = 20;

        $pagerfanta = (new Pagerfanta(new DoctrineORMAdapter($callbacksQb, false)))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($request->query->get('page', 1));

        $callbacks = iterator_to_array($pagerfanta->getIterator());

        if ($request->isXmlHttpRequest()) {
            return $this->render(
                '@MetalPrivateOffice/partials/archive_callbacks_list.html.twig',
                array(
                    'callbacks' => $callbacks,
                    'pagerfanta' => $pagerfanta
                )
            );
        }

        return $this->render(
            '@MetalPrivateOffice/PrivateArchive/callbacks.html.twig',
            array(
                'callbacks' => $callbacks,
                'pagerfanta' => $pagerfanta
            )
        );
    }
}
