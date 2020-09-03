<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\ComplaintsBundle\Entity\AbstractComplaint;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PrivateComplaintsController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUPPLIER')")
     */
    public function listAction(Request $request)
    {
        $queryBag = $request->query;
        $notProcessedFilter = $queryBag->get('notProcessed');
        $companyId = $this->getUser()->getCompany()->getId();
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $complaintRepository = $em->getRepository('MetalComplaintsBundle:AbstractComplaint');

        $complaintsQb = $complaintRepository->createQueryBuilder('c')
            ->andWhere('c.company = :company_id')
            ->orderBy('c.createdAt', 'DESC')
            ->setParameter('company_id', $companyId);
        if ($notProcessedFilter) {
            $complaintsQb->andWhere('c.processedAt IS NULL');
        }

        $filter = $queryBag->get('filter');
        if ($filter == 'watched') {
            $complaintsQb->andWhere('c.viewedBy IS NOT NULL');
        } else {
            $complaintsQb->andWhere('c.viewedBy IS NULL');
        }

        $complaints = $complaintsQb->getQuery()->getResult();

        $complaintRepository->attachProductsToComplaints($complaints);

        $adapter = new DoctrineORMAdapter($complaintsQb, false);
        $pagerfanta = new Pagerfanta($adapter);

        $perPage = 10;
        $pagerfanta
            ->setMaxPerPage($perPage)
            ->setCurrentPage($queryBag->get('page', 1));

        $complaints = iterator_to_array($pagerfanta->getIterator());

        $complaintRepository->setComplaintsViewed($complaints, $user);

        if ($request->isXmlHttpRequest()) {

            return $this->render('MetalPrivateOfficeBundle:partials:complaints_list.html.twig', array(
                'complaints' => $complaints,
                'pagerfanta' => $pagerfanta
            ));
        }

        return $this->render('MetalPrivateOfficeBundle:PrivateComplaint:list.html.twig', array(
            'complaints' => $complaints,
            'pagerfanta' => $pagerfanta
        ));
    }

    /**
     * @ParamConverter("complaint", class="MetalComplaintsBundle:AbstractComplaint")
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL') and (complaint.getCompany().getId() == user.getCompany().getId())")
     */
    public function processComplaintAction(AbstractComplaint $complaint)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $complaint->setProcessed();

        $em->flush();

        return JsonResponse::create(
            array(
                'status' => 'success',
                'date' => $this->get('brouzie.helper_factory')
                    ->get('MetalProjectBundle:Formatting')
                    ->formatDateTime($complaint->getProcessedAt())
            )
        );
    }
}
