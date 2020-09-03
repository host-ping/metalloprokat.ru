<?php

namespace Metal\UsersBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\DemandsBundle\DataFetching\Spec\DemandFilteringSpec;
use Metal\DemandsBundle\DataFetching\Spec\DemandOrderingSpec;
use Metal\TerritorialBundle\Entity\Country;
use Metal\UsersBundle\Entity\Favorite;
use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Repository\FavoriteCompanyRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FavoritesController extends Controller
{
    private $companiesPerPage = 20;
    private $demandsPerPage = 20;

    /**
     * @Security("has_role('ROLE_USER') and user.isAllowedAddInFavorite()")
     */
    public function listAction(Request $request, Country $country)
    {
        $queryBag = $request->query;
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $user = $this->getUser();
        /* @var $user User */

        // favorite company of user
        $favoriteCompaniesQb = $em->createQueryBuilder()
            ->select('fc')
            ->from('MetalUsersBundle:FavoriteCompany', 'fc')
            ->leftJoin('fc.company', 'c')
            ->addSelect('c')
            ->andWhere('fc.user = :user')
            ->setParameter('user', $user->getId());

        // favorite company of user with/without comment
        $withComments = $queryBag->get('comment');
        if ($withComments !== null) {
            if ($withComments === 'with') {
                $favoriteCompaniesQb
                    ->andWhere("fc.comment != ''");
            } elseif ($withComments === 'without') {
                $favoriteCompaniesQb
                    ->andWhere("fc.comment = ''");
            }
        }

        // sort by reviews or data create in favorite
        $orders = $queryBag->get('order');
        if ($orders === 'reviews') {
            $favoriteCompaniesQb
                ->join('MetalCompaniesBundle:CompanyCounter', 'cc', Expr\Join::WITH, 'cc.company = c.id')
                ->orderBy('cc.reviewsCount', 'DESC');
        } elseif ($orders === 'company_rating') {
            $favoriteCompaniesQb
                ->orderBy('c.companyRating', 'DESC');
        } else {
            $favoriteCompaniesQb
                ->orderBy('fc.createdAt', 'DESC');
        }

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($favoriteCompaniesQb, false));
        $pagerfanta
            ->setMaxPerPage($this->companiesPerPage)
            ->setCurrentPage($queryBag->get('page', 1));

        $favoriteCompanies = iterator_to_array($pagerfanta->getIterator());

        $favoriteCompanyRepository = $em->getRepository('MetalUsersBundle:FavoriteCompany');
        /* @var $favoriteCompanyRepository FavoriteCompanyRepository */
        $favoriteCompanyRepository->attachProductsCount($favoriteCompanies);
        $favoriteCompanyRepository->attachProducts($favoriteCompanies, $country);

        $companyCounterRepository = $em->getRepository('MetalCompaniesBundle:CompanyCounter');
        $companies = array();
        foreach ($favoriteCompanies as $favCompany) {
            $companies[$favCompany->getCompany()->getId()] = $favCompany->getCompany();
        }
        $companyCounterRepository->attachCompanyCounter($companies);

        if ($request->isXmlHttpRequest()) {
            $response = JsonResponse::create(
                array(
                    'page.favorites_companies_list' => $this->renderView(
                        '@MetalCompanies/partial/companies_in_list.html.twig',
                        array(
                            'favoriteCompanies' => $favoriteCompanies,
                            'pagerfanta' => $pagerfanta,
                        )
                    )
                )
            );

            $response->headers->addCacheControlDirective('no-store', true);

            return $response;
        }

        return $this->render(
            '@MetalUsers/Favorites/list_favorites.html.twig',
            array(
                'favoriteCompanies' => $favoriteCompanies,
                'pagerfanta' => $pagerfanta,
                'userCounter' => $user->getCounter(),
            )
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function demandsListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $user = $this->getUser();
        /* @var $user User */
        $favoriteDemandQb = $this->getDemandsQb($request);

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($favoriteDemandQb, false));
        $pagerfanta
            ->setMaxPerPage($this->demandsPerPage)
            ->setCurrentPage($request->query->get('page', 1));

        $favoriteDemands = iterator_to_array($pagerfanta->getIterator());

        $demands = array();
        foreach ($favoriteDemands as $favoriteDemand) {
            $demands[] = $favoriteDemand->getDemand();
            $favoriteDemand->getDemand()->setAttribute('isInFavorite', true);
        }

        $demandItemRepository = $em->getRepository('MetalDemandsBundle:DemandItem');
        $demandItemRepository->attachDemandItems($demands);

        $demandFileRepository = $em->getRepository('MetalDemandsBundle:DemandFile');
        $demandFileRepository->attachDemandFiles($demands);

        if ($request->isXmlHttpRequest()) {
            $response = JsonResponse::create(
                array(
                    'page.favorites_demands_list' => $this->renderView(
                        '@MetalDemands/partials/demands_in_list.html.twig',
                        array(
                            'favoriteDemands' => $favoriteDemands,
                            'pagerfanta' => $pagerfanta,
                        )
                    )
                )
            );

            $response->headers->addCacheControlDirective('no-store', true);

            return $response;
        }

        return $this->render(
            '@MetalUsers/Favorites/list_favorites_demands.html.twig',
            array(
                'favoriteDemands' => $favoriteDemands,
                'pagerfanta' => $pagerfanta,
                'userCounter' => $user->getCounter(),
            )
        );
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL') and user.getCanUseService() and user.getCompany().getPackageChecker().isAllowedExportDemands()")
     */
    public function exportAction(Request $request, $format)
    {
        set_time_limit(600);
        $user = $this->getUser();
        /* @var $user User */

        $demandExportService = $this->get('metal.demands.demand_export_service');
        $demandsQb = $this->getDemandsQb($request);

        $demandsIds = $demandsQb
            ->select('d.id')
            ->setMaxResults(AbstractDemand::EXPORT_LIMIT)
            ->getQuery()
            ->getResult();

        $demandsIds = array_column($demandsIds, 'id');

        $criteria = new DemandFilteringSpec();
        $orderBy = new DemandOrderingSpec();
        $orderBy->favoriteOrder($request->query->get('order'))
            ->favoriteComment($request->query->get('comment'));

        $fileName = $demandExportService->getExportFileName(
            $demandsIds,
            $user->getCompany(),
            $format,
            'favorite_demands',
            $user,
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

    protected function getDemandsQb(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $user = $this->getUser();
        /* @var $user User */

        $query = $request->query;

        // favorite demand
        $favoriteDemandQb = $em->createQueryBuilder()
            ->select('f')
            ->from('MetalUsersBundle:Favorite', 'f')
            ->join('f.demand', 'd')
            ->addSelect('d')
            ->andWhere('d.moderatedAt IS NOT NULL')
            ->andWhere('d.category IS NOT NULL')
            ->andWhere('f.user = :user')
            ->setParameter('user', $user);

        // favorite demand  with/without comment
        $withComments = $query->get('comment');
        if ($withComments !== null) {
            if ($withComments === 'with') {
                $favoriteDemandQb
                    ->andWhere("f.comment != ''");
            } elseif ($withComments === 'without') {
                $favoriteDemandQb
                    ->andWhere("f.comment = ''");
            }
        }

        // sort by answersCount or viewsCount in demand
        $orders = $query->get('order');
        if ($orders === 'answersCount') {
            $favoriteDemandQb
                ->orderBy('d.answersCount', 'DESC');
        } elseif ($orders === 'viewsCount') {
            $favoriteDemandQb
                ->orderBy('d.viewsCount', 'DESC');
        } else {
            $favoriteDemandQb
                ->orderBy('f.createdAt', 'DESC');
        }

        return $favoriteDemandQb;
    }
}
