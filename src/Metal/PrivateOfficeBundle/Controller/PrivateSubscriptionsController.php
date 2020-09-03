<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Metal\DemandsBundle\DataFetching\Spec\DemandFilteringSpec;
use Metal\DemandsBundle\DataFetching\Spec\DemandOrderingSpec;
use Metal\DemandsBundle\Helper\DefaultHelper;
use Metal\NewsletterBundle\Entity\Subscriber;
use Metal\TerritorialBundle\Entity\Country;
use Metal\UsersBundle\Entity\User;
use Metal\DemandsBundle\DataFetching\Spec\DemandLoadingSpec;
use Metal\DemandsBundle\DataFetching\FacetIntervalResultExtractor;
use Metal\DemandsBundle\DataFetching\Spec\DemandFacetSpec;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\JsonResponse;

class PrivateSubscriptionsController extends Controller
{
    protected $demandsPerPage = 20;

    /**
     * @Security("has_role('ROLE_SUPPLIER')")
     */
    public function demandsListAction(Request $request, Country $country)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $demandsDataFetcher = $this->container->get('metal.demands.data_fetcher');
        $demandsEntityLoader = $this->container->get('metal.demands.entity_loader');

        list($specification, $orderBy) = $this->prepareCriteria($request, $country);

        $pagerfanta = $demandsDataFetcher->getPagerfantaByCriteria(
            $specification,
            $orderBy,
            $this->demandsPerPage,
            $request->query->get('page', 1)
        );

        $loaderOpts = new DemandLoadingSpec();

        $loaderOpts->attachCitiesAndRegions(true);

        $demandsListResultsViewModel = $demandsEntityLoader->getListResultsViewModel($pagerfanta, $loaderOpts);

        if ($request->isXmlHttpRequest()) {
            return $this->render(
                '@MetalDemands/partials/demands_list.html.twig',
                array(
                    'pagerfanta' => $demandsListResultsViewModel->pagerfanta,
                    'use_pagination' => false,
                )
            );
        }

        $facetSpec = new DemandFacetSpec();
        $facetSpec->facetByCreatedAt($specification);
        $facetsResultSet = $demandsDataFetcher->getFacetedResultSetByCriteria($specification, $facetSpec);
        $facetResultExtractor = new FacetIntervalResultExtractor($facetsResultSet, DemandFacetSpec::COLUMN_CREATED_AT_INTERVAL);
        $facetResultExtractor->appendInterval('demands_count_custom', $demandsListResultsViewModel->count);

        $subscriber = $em->getRepository('MetalNewsletterBundle:Subscriber')->findOneBy(array('user' => $this->getUser()));

        return $this->render('@MetalPrivateOffice/PrivateSubscriptions/listDemands.html.twig', array(
            'demandsViewModel' => $demandsListResultsViewModel,
            'demandsCountByPeriod' => $facetResultExtractor->getIntervals(),
            'criteria' => $specification,
            'subscribedForDemands' => $subscriber->getSubscribedForDemands(),
            'demandSubscriptionStatuses' => Subscriber::getDemandSubscriptionStatusesAsArray(false),
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL') and user.getCanUseService() and user.getCompany().getPackageChecker().isAllowedExportDemands()")
     */
    public function exportAction(Request $request ,Country $country, $format)
    {
        set_time_limit(600);
        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        $demandExportService = $this->get('metal.demands.demand_export_service');
        $demandsDataFetcher = $this->container->get('metal.demands.data_fetcher');

        list($criteria, $orderBy) = $this->prepareCriteria($request, $country);
        $resultSet = $demandsDataFetcher->getResultSetByCriteria($criteria, $orderBy, 1000);
        $demandsIds = array_column(iterator_to_array($resultSet), 'id');

        $fileName = $demandExportService->getExportFileName(
            $demandsIds,
            $company,
            $format,
            'subscription_demands',
            $this->getUser(),
            $criteria,
            $orderBy,
            $request
        );

        $dir = $this->container->getParameter('upload_dir');

        $response = new BinaryFileResponse($dir.'/demands-export/'.$fileName);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf('Потребители %s.%s', $this->container->getParameter('project.title'), $format),
            $fileName
        );

        return $response;
    }

    private function prepareCriteria(Request $request, Country $country)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = $this->getUser();
        /* @var $user User */

        $criteria = new DemandFilteringSpec();
        $criteria->country($country);

        list($periodFrom, $periodTo) = DefaultHelper::determinatePeriod($request);
        $criteria->dateFrom($periodFrom);
        $criteria->dateTo($periodTo);

        $categoriesIds = $em->getRepository('MetalDemandsBundle:DemandSubscriptionCategory')
            ->getCategoryIdsPerUser(array($user->getId()))[$user->getId()];

        if ($categoriesIds) {
            $criteria->categoriesIds($categoriesIds);
        }

        $territorialIds = $em->getRepository('MetalDemandsBundle:DemandSubscriptionTerritorial')
            ->getSubscribedTerritorialIdsPerUser(array($user->getId()))[$user->getId()];

        if ($territorialIds) {
            $criteria->territorialStructureIds($territorialIds);
        }

        $orderBy = new DemandOrderingSpec();

        $orderBy->createdAt();

        return array($criteria, $orderBy);
    }

}
