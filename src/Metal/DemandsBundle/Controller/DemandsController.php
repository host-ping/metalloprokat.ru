<?php

namespace Metal\DemandsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CompaniesBundle\Entity\Company;
use Metal\DemandsBundle\DataFetching\FacetIntervalResultExtractor;
use Metal\DemandsBundle\DataFetching\Spec\DemandFacetSpec;
use Metal\DemandsBundle\DataFetching\Spec\DemandFilteringSpec;
use Metal\DemandsBundle\DataFetching\Spec\DemandLoadingSpec;
use Metal\DemandsBundle\DataFetching\Spec\DemandOrderingSpec;
use Metal\DemandsBundle\Entity\Demand;
use Metal\DemandsBundle\Helper\DefaultHelper;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Region;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class DemandsController extends Controller
{
    protected $demandsPerPage = 20;

    public function listAction(Request $request, Category $category = null)
    {
        $page = $request->query->get('page', 1);

        $demandsDataFetcher = $this->get('metal.demands.data_fetcher');
        $demandsEntityLoader = $this->get('metal.demands.entity_loader');

        $criteria = DemandFilteringSpec::createFromRequest($request);
        $orderBy = DemandOrderingSpec::createFromRequest($request);
        $loaderOpts = new DemandLoadingSpec();
        if ($category) {
            $loaderOpts->attachDemandItem(true, $category->getId());
        }

        $loaderOpts->attachCitiesAndRegions(true);

        $demandsPagerfanta = $demandsDataFetcher->getPagerfantaByCriteria($criteria, $orderBy, $this->demandsPerPage, $page);
        $demandsListResultsViewModel = $demandsEntityLoader->getListResultsViewModel($demandsPagerfanta, $loaderOpts);

        if ($request->isXmlHttpRequest()) {
            $response = JsonResponse::create(
                array(
                    'page.title' => $this->get('brouzie.helper_factory')->get('MetalDemandsBundle:DemandsListSeo')->getMetaTitleForDemandsPage(),
                    'page.demands_list' => $this->renderView(
                        '@MetalDemands/partials/demands_list.html.twig',
                        array(
                            'pagerfanta' => $demandsListResultsViewModel->pagerfanta,
                            'category' => $category,
                            'options' => array('include_banners' => true),
                        )
                    )
                )
            );

            $response->headers->addCacheControlDirective('no-store', true);

            return $response;
        }

        $facetSpec = new DemandFacetSpec();
        $facetSpec->facetByCreatedAt($criteria);
        $facetsResultSet = $demandsDataFetcher->getFacetedResultSetByCriteria($criteria, $facetSpec);
        $facetResultExtractor = new FacetIntervalResultExtractor($facetsResultSet, DemandFacetSpec::COLUMN_CREATED_AT_INTERVAL);
        $facetResultExtractor->appendInterval('demands_count_custom', $demandsListResultsViewModel->count);

        return $this->render('@MetalDemands/Demands/list.html.twig', array(
            'demandsViewModel' => $demandsListResultsViewModel,
            'category' => $category,
            'demandsCountByPeriod' => $facetResultExtractor->getIntervals(),
            'criteria' => $criteria,
        ));
    }

    public function searchAction(Request $request, City $city = null, Region $region = null)
    {
        $query = $request->query->get('query');
        $category = null;
        $categoryId = $request->query->get('category');

        $demandsHelper = $this->container->get('brouzie.helper_factory')->get('MetalDemandsBundle');
        /* @var $demandsHelper DefaultHelper */

        $subdomain = 'www';
        if ($city) {
            $subdomain = $city->getSlugWithFallback();
        } elseif ($region) {
            $subdomain = $region->getSlug();
        }

        $em = $this->getDoctrine()->getManager();
        if ($categoryId) {
            $category = $em->find('MetalCategoriesBundle:Category', $categoryId);
        }

        if (!$query) {
            $routeParameters = array(
                'subdomain' => $subdomain,
            );

            if ($category) {
                $routeParameters['category_slug'] = $category->getSlugCombined();

                if ($categoryId) {
                    $routeParameters['context'] = 1;
                }

                return $this->redirect($this->generateUrl('MetalDemandsBundle:Demands:list_subdomain', $routeParameters));
            }

            return $this->redirect($this->generateUrl('MetalDemandsBundle:Demands:list_subdomain', $routeParameters));
        }

        $activeCategory = null;
        $redirectRequired = false;
        if ($category) {
            $activeCategory = $category;
        } else {
//            $categoryDetector = $this->container->get('metal.categories.category_matcher');
//            $activeCategory = $categoryDetector->getCategoryByTitle($query);
//            $redirectRequired = true;
            $activeCategory = null;
        }

        if ($redirectRequired && $activeCategory) {
            $routeParameters = array(
                'subdomain' => $subdomain,
                'category_slug' => $activeCategory->getSlugCombined(),
            );

            return $this->redirect($this->generateUrl('MetalDemandsBundle:Demands:list_subdomain_category', $routeParameters));
        }

        if ($id = Demand::extractDemandNumberFromSearchString($query)) {
            $demand = $this->getDoctrine()->getRepository('MetalDemandsBundle:Demand')
                ->find($id);

            if ($demand && $demand->isModerated() && !$demand->isDeleted()) {
                return $this->redirect($demandsHelper->generateDemandUrl($demand));
            }
        }

        $routeParameters =  array(
            'subdomain' => $subdomain,
            'q' => $query,
        );

        if ($category) {
            $routeParameters['category_slug'] = $category->getSlugCombined();
            $routeParameters['context'] = 1;

            return $this->redirect($this->generateUrl('MetalDemandsBundle:Demands:list_subdomain_category', $routeParameters));
        }

        return $this->redirect($this->generateUrl('MetalDemandsBundle:Demands:list_subdomain', $routeParameters));
    }

    public function informerAction()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $limit = 100;
        $lightDemands = $em->getRepository('MetalDemandsBundle:Demand')->createQueryBuilder('d')
            ->select('d.id')
            ->join('d.category', 'dc')
            ->where('d.moderatedAt < :date')
            ->andWhere('d.deletedAt IS NULL')
            ->andWhere('d.moderatedAt IS NOT NULL')
            ->setParameter('date', new \DateTime('-2 day'))
            ->orderBy('d.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        $demandsIds = array();
        foreach ($lightDemands as $demand) {
            $demandsIds[$demand['id']] = $demand['id'];
        }

        $demands = $em->getRepository('MetalDemandsBundle:Demand')->createQueryBuilder('d')
            ->where('d.id IN (:demands_ids)')
            ->setParameter('demands_ids', array_keys($demandsIds))
            ->orderBy('d.id', 'DESC')
            ->getQuery()
            ->getResult();

        $em->getRepository('MetalDemandsBundle:DemandItem')->attachDemandItems($demands);
        $em->getRepository('MetalDemandsBundle:Demand')->attachCitiesToDemands($demands, true);

        $normalizer = function($txt) {
            $search = array(
                chr(13).chr(10),
                chr(9),
                chr(13),
                chr(10),
            );

            return mb_convert_encoding(str_replace($search, ' ', $txt), 'Windows-1251');
        };

        $lines = array();
        foreach ($demands as $demand) {
            /* @var $demand Demand */

            $columns = null;
            foreach ($demand->getAttribute('demandItems') as $demandItem) {
                if (!$demandItem->getCategory()) {
                    continue;
                }
                $demandVolume = $demandItem->getVolume() ? $demandItem->getVolume().' '.$demandItem->getVolumeTypeTitle() : 'объем договорной';
                $columns = array(
                    $demand->getId(),
                    $demand->getCity()->getTitle(),
                    $demand->getCity()->getRegion()->getId(),
                    $demand->getCity()->getRegion()->getTitle(),
                    $demandItem->getCategory()->getTitle(),
                    mb_substr($demandItem->getTitle(), 0, 255),
                    $demand->getDemandPeriodicityTitle(),
                    mb_substr($demandVolume, 0, 255),
                    $demand->getDeadline().' дней'
                );
            }

            if (!$columns) {
                continue;
            }

            $lines[] = implode(chr(9), array_map($normalizer, $columns));
        }

        return Response::create(implode(chr(13).chr(10), $lines), 200, array('Content-Type' => 'text/plain;charset=Windows-1251'));
    }

    public function informerVersionAction()
    {
        return Response::create('2.0');
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL') and user.getCanUseService() and user.getCompany().getPackageChecker().isAllowedExportDemands()")
     */
    public function exportAction(Request $request, $format)
    {
        set_time_limit(600);

        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        $demandExportService = $this->get('metal.demands.demand_export_service');
        $demandsDataFetcher = $this->get('metal.demands.data_fetcher');

        $criteria = DemandFilteringSpec::createFromRequest($request);
        $orderBy = DemandOrderingSpec::createFromRequest($request);

        $resultSet = $demandsDataFetcher->getResultSetByCriteria($criteria, $orderBy, 1000);
        $demandsIds = array_column(iterator_to_array($resultSet), 'id');

        $fileName = $demandExportService->getExportFileName(
            $demandsIds,
            $company,
            $format,
            'demands',
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
}
