<?php

namespace Metal\StatisticBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyTypeProvider;
use Metal\ProductsBundle\Entity\Product;
use Sonata\AdminBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AllStatisticAdminController extends CoreController
{
    private $cacheTtl = 18000;

    /**
     * @Security("has_role('ROLE_MANAGER') or has_role('ROLE_SEO_ADMINISTRATOR')")
     */
    public function viewAction(Request $request)
    {
        ini_set('memory_limit', '1024M');

        $year = $request->query->get('year') ?: date('Y');

        $dateFrom = new \DateTime(sprintf('%s-01-01', $year));
        $dateTo = new \DateTime(sprintf('%s-12-31', $year));

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $qbs = array();

        $companiesCountPerMonth = $em
            ->getRepository('MetalCompaniesBundle:Company')
            ->createQueryBuilder('c')
            ->select('MONTH(c.createdAt) AS _group_column')
            ->addSelect('COUNT(c.id) AS _count')
            ->addSelect('c.companyTypeId AS company_type')
            ->join('c.companyLog', 'cl')
            ->join('cl.createdBy', 'u')
            ->where('u.isEmailConfirmed = true')
            ->andWhere('c.createdAt BETWEEN :date_from AND :date_to')
            ->andWhere('(c.deletedAtTS IS NULL OR c.deletedAtTS = 0)')
            ->groupBy('_group_column')
            ->addGroupBy('company_type')
            ->setParameter('date_from', $dateFrom)
            ->setParameter('date_to', $dateTo)
            ->getQuery()
            ->useResultCache(true, $this->cacheTtl)
            ->getResult();

        $qbs['users_count'] = $em
            ->getRepository('MetalUsersBundle:User')
            ->createQueryBuilder('u')
            ->select('MONTH(u.createdAt) AS _group_column')
            ->addSelect('COUNT(u.id) AS _count')
            ->where('u.isEmailConfirmed = true')
            ->andWhere('u.isEnabled = true')
            ->andWhere('u.createdAt BETWEEN :date_from AND :date_to');

        $qbs['consumers_count'] = $em
            ->getRepository('MetalUsersBundle:User')
            ->createQueryBuilder('u')
            ->select('MONTH(u.createdAt) AS _group_column')
            ->addSelect('COUNT(u.id) AS _count')
            ->where('u.isEmailConfirmed = true')
            ->andWhere('u.isEnabled = true')
            ->andWhere('u.company IS NULL')
            ->andWhere('u.createdAt BETWEEN :date_from AND :date_to');

        $qbs['active_users_count'] = $em
            ->getRepository('MetalDemandsBundle:AbstractDemand')
            ->createQueryBuilder('ad')
            ->select('MONTH(ad.createdAt) AS _group_column')
            ->addSelect('COUNT(DISTINCT ad.user) AS _count')
            ->andWhere('ad.createdAt BETWEEN :date_from AND :date_to');

        $qbs['active_users_count_increment'] = $em
            ->getRepository('MetalDemandsBundle:AbstractDemand')
            ->createQueryBuilder('ad')
            ->select('MONTH(ad.createdAt) AS _group_column')
            ->addSelect('COUNT(DISTINCT ad.email) AS _count')
            ->where('ad.user IS NULL')
            ->andWhere('ad.createdAt BETWEEN :date_from AND :date_to')
            ->andWhere("ad.email <> ''")
            ->andWhere('ad.email IS NOT NULL');

        $qbs['active_users_count_increment_1'] = $em
            ->getRepository('MetalDemandsBundle:AbstractDemand')
            ->createQueryBuilder('ad')
            ->select('MONTH(ad.createdAt) AS _group_column')
            ->addSelect('COUNT(DISTINCT ad.phone) AS _count')
            ->where('ad.user IS NULL')
            ->andWhere('ad.createdAt BETWEEN :date_from AND :date_to')
            ->andWhere('ad.email IS NULL');

        $qbs['show_products_count'] = $em->getRepository('MetalStatisticBundle:StatsDaily')
            ->createQueryBuilder('sd')
            ->select('COALESCE(SUM(sd.reviewsProductsCount), 0) AS _count')
            ->addSelect('MONTH(sd.date) AS _group_column')
            ->where('sd.date BETWEEN :date_from AND :date_to');

        $qbs['demands_count'] = $em->getRepository('MetalDemandsBundle:Demand')
            ->createQueryBuilder('d')
            ->select('MONTH(d.createdAt) AS _group_column')
            ->addSelect('COUNT(d.id) AS _count')
            ->andWhere('d.createdAt BETWEEN :date_from AND :date_to')
            ->andWhere('d.moderatedAt IS NOT NULL AND d.deletedAt IS NULL');

        $qbs['demands_count_not_grabbers'] = clone $qbs['demands_count'];
        $qbs['demands_count_not_grabbers']->andWhere('d.parsedDemand IS NULL');

        $qbs['demands_without_views_count'] = $em->getRepository('MetalDemandsBundle:Demand')
            ->createQueryBuilder('d')
            ->select('MONTH(d.createdAt) AS _group_column')
            ->addSelect('COUNT(d.id) AS _count')
            ->leftJoin('MetalDemandsBundle:DemandView', 'dv', 'WITH', 'd.id = dv.demand')
            ->where('dv.id IS NULL')
            ->andWhere('d.createdAt BETWEEN :date_from AND :date_to')
            ->andWhere('d.moderatedAt IS NOT NULL AND d.deletedAt IS NULL');

        $qbs['demands_count_increment'] = $em->getRepository('MetalDemandsBundle:PrivateDemand')
            ->createQueryBuilder('pd')
            ->select('MONTH(pd.createdAt) AS _group_column')
            ->addSelect('COUNT(pd.id) AS _count')
            ->andWhere('pd.createdAt BETWEEN :date_from AND :date_to')
            ->andWhere('pd.deletedAt IS NULL')
            ->groupBy('_group_column');

        $qbs['private_demands_count_not_grabbers'] = clone $qbs['demands_count_increment'];

        $demandsViewsCountPerMonth = $em->getRepository('MetalDemandsBundle:DemandView')
            ->createQueryBuilder('dv')
            ->select('MONTH(dv.viewedAt) AS _group_column')
            ->addSelect('COUNT(dv.id) AS _count')
            ->addSelect('IDENTITY(u.company) as companyId')
            ->join('dv.user', 'u')
            ->andWhere('dv.viewedAt BETWEEN :date_from AND :date_to')
            ->groupBy('_group_column')
            ->addGroupBy('companyId')
            ->addGroupBy('dv.demand')
            ->setParameter('date_from', $dateFrom)
            ->setParameter('date_to', $dateTo)
            ->getQuery()
            ->useResultCache(true, $this->cacheTtl)
            ->getResult();

        $demandsAnswersCountPerMonth = $em->getRepository('MetalDemandsBundle:DemandAnswer')
            ->createQueryBuilder('da')
            ->select('MONTH(da.createdAt) AS _group_column')
            ->addSelect('COUNT(da.id) AS _count')
            ->addSelect('IDENTITY(u.company) as companyId')
            ->join('da.user', 'u')
            ->andWhere('da.createdAt BETWEEN :date_from AND :date_to')
            ->groupBy('_group_column')
            ->addGroupBy('companyId')
            ->addGroupBy('da.demand')
            ->setParameter('date_from', $dateFrom)
            ->setParameter('date_to', $dateTo)
            ->getQuery()
            ->useResultCache(true, $this->cacheTtl)
            ->getResult();

        $qbs['added_products_count'] = $em->getRepository('MetalStatisticBundle:StatsProductChangePerMonth')
            ->createQueryBuilder('statsProductChangePerMonth')
            ->select('MONTH(statsProductChangePerMonth.date) AS _group_column')
            ->addSelect('statsProductChangePerMonth.count AS _count')
            ->andWhere('statsProductChangePerMonth.date BETWEEN :date_from AND :date_to');

        $qbs['deleted_products_count'] = $em->getRepository('MetalProductsBundle:Product')
            ->createQueryBuilder('p')
            ->select('MONTH(p.updatedAt) AS _group_column')
            ->addSelect('COUNT(p.id) AS _count')
            ->andWhere('p.updatedAt BETWEEN :date_from AND :date_to')
            ->andWhere('p.checked = :status_deleted')
            ->setParameter('status_deleted', Product::STATUS_DELETED);

        $activeUsersByPeriods = array();
        if ($year == date('Y')) {
            $activeUsersByPeriods = array(
                1 => 0,
                3 => 0,
                6 => 0,
                12 => 0
            );
            foreach ($activeUsersByPeriods as $period => $activeUser) {
                $count = $em
                    ->getRepository('MetalDemandsBundle:AbstractDemand')
                    ->createQueryBuilder('ad')
                    ->select('COUNT(DISTINCT ad.user) AS _count')
                    ->andWhere('ad.createdAt BETWEEN :date_from AND :date_to')
                    ->setParameter('date_from', new \DateTime(sprintf('-%d month', $period)))
                    ->setParameter('date_to', new \DateTime())
                    ->getQuery()
                    ->getOneOrNullResult();
                $activeUsersByPeriods[$period] = $count['_count'];

                $count = $em
                    ->getRepository('MetalDemandsBundle:AbstractDemand')
                    ->createQueryBuilder('ad')
                    ->select('COUNT(DISTINCT ad.email) AS _count')
                    ->andWhere('ad.createdAt BETWEEN :date_from AND :date_to')
                    ->andWhere('ad.user IS NULL')
                    ->andWhere("ad.email <> ''")
                    ->andWhere('ad.email IS NOT NULL')
                    ->setParameter('date_from', new \DateTime(sprintf('-%d month', $period)))
                    ->setParameter('date_to', new \DateTime())
                    ->getQuery()
                    ->getOneOrNullResult();
                $activeUsersByPeriods[$period] += $count['_count'];

                $count = $em
                    ->getRepository('MetalDemandsBundle:AbstractDemand')
                    ->createQueryBuilder('ad')
                    ->select('COUNT(DISTINCT ad.phone) AS _count')
                    ->andWhere('ad.createdAt BETWEEN :date_from AND :date_to')
                    ->andWhere('ad.user IS NULL')
                    ->andWhere('ad.email IS NULL')
                    ->setParameter('date_from', new \DateTime(sprintf('-%d month', $period)))
                    ->setParameter('date_to', new \DateTime())
                    ->getQuery()
                    ->getOneOrNullResult();
                $activeUsersByPeriods[$period] += $count['_count'];
            }
        }

        $stats = array();
        $demandsViewsCountPerMonthWithGrouping = array();
        foreach ($demandsViewsCountPerMonth as $countPerMonth) {
            if (!isset($demandsViewsCountPerMonthWithGrouping[$countPerMonth['_group_column']])) {
                $demandsViewsCountPerMonthWithGrouping[$countPerMonth['_group_column']] = array(
                    '_group_column' => $countPerMonth['_group_column'],
                    '_count' => 1,
                );
            } else {
                $demandsViewsCountPerMonthWithGrouping[$countPerMonth['_group_column']] = array(
                    '_group_column' => $countPerMonth['_group_column'],
                    '_count' => $demandsViewsCountPerMonthWithGrouping[$countPerMonth['_group_column']]['_count'] + 1,
                );
            }
        }
        $demandsViewsCountPerMonth = array_values($demandsViewsCountPerMonthWithGrouping);

        $demandsAnswersCountPerMonthWithGrouping = array();
        foreach ($demandsAnswersCountPerMonth as $countPerMonth) {
            if (!isset($demandsAnswersCountPerMonthWithGrouping[$countPerMonth['_group_column']])) {
                $demandsAnswersCountPerMonthWithGrouping[$countPerMonth['_group_column']] = array(
                    '_group_column' => $countPerMonth['_group_column'],
                    '_count' => 1,
                );
            } else {
                $demandsAnswersCountPerMonthWithGrouping[$countPerMonth['_group_column']] = array(
                    '_group_column' => $countPerMonth['_group_column'],
                    '_count' => $demandsAnswersCountPerMonthWithGrouping[$countPerMonth['_group_column']]['_count'] + 1,
                );
            }
        }
        $demandsAnswersCountPerMonth = array_values($demandsAnswersCountPerMonthWithGrouping);

        $companiesTypes = CompanyTypeProvider::getAllTypes();

        $statsCompaniesByTypeOfOwnership = array();
        foreach ($companiesCountPerMonth as $countPerMonth) {
            if (!isset($stats[$countPerMonth['_group_column']])) {
                $stats[$countPerMonth['_group_column']]['companies_count'] = $countPerMonth['_count'];
            } else {
                $stats[$countPerMonth['_group_column']]['companies_count'] += $countPerMonth['_count'];
            }

            $statsCompaniesByTypeOfOwnership[$countPerMonth['_group_column']][$countPerMonth['company_type']] = $countPerMonth['_count'];
        }

        foreach ($demandsViewsCountPerMonth as $countPerMonth) {
            $stats[$countPerMonth['_group_column']]['demands_views_count'] = $countPerMonth['_count'];
            $stats[$countPerMonth['_group_column']]['demands_responses_count'] = (int)$countPerMonth['_count'];
        }

        foreach ($demandsAnswersCountPerMonth as $countPerMonth) {
            $stats[$countPerMonth['_group_column']]['demands_responses_count'] += (int)$countPerMonth['_count'];
        }

        foreach ($qbs as $name => $qb) {
            $result = $qb
                ->setParameter('date_from', $dateFrom)
                ->setParameter('date_to', $dateTo)
                ->addGroupBy('_group_column')
                ->getQuery()
                ->useResultCache(true, $this->cacheTtl)
                ->getResult();

            $this->counting($stats, $result, $name);
        }


        $currentProductsCountPerMonth = $em->getConnection()->fetchAll("
            SELECT sq._group_column, (
                SELECT COUNT(*)
                    FROM Message142 pc
                    WHERE pc.Created <= max_created
                    ) AS _count
                FROM (
                    SELECT
                    MAX(p.Created) AS max_created,
                    MONTH(p.Created) AS _group_column
                    FROM Message142 p
                    WHERE p.Created BETWEEN :dateFrom AND :dateTo
                    GROUP BY _group_column
                ) sq
        ", array('dateFrom' => $dateFrom, 'dateTo' => $dateTo), array('dateFrom' => 'datetime', 'dateTo' => 'datetime'));

        $this->counting($stats, $currentProductsCountPerMonth, 'current_products_count');

        $currentCompaniesCountPerMonth = $em->getConnection()->fetchAll("
            SELECT sq._group_column, (
                SELECT COUNT(*)
                    FROM Message75 pc
                    WHERE pc.Created <= max_created
                    ) AS _count
                FROM (
                    SELECT
                    MAX(p.Created) AS max_created,
                    MONTH(p.Created) AS _group_column
                    FROM Message75 p
                    WHERE p.Created BETWEEN :dateFrom AND :dateTo
                    GROUP BY _group_column
                ) sq
        ", array('dateFrom' => $dateFrom, 'dateTo' => $dateTo), array('dateFrom' => 'datetime', 'dateTo' => 'datetime'));

        $this->counting($stats, $currentCompaniesCountPerMonth, 'current_companies_count');

        return $this->render(
            '@MetalStatistic/Admin/view.html.twig',
            array(
                'stats' => $stats,
                'statsCompaniesByTypeOfOwnership' => $statsCompaniesByTypeOfOwnership,
                'companiesTypes' => $companiesTypes,
                'activeUsersByPeriods' => $activeUsersByPeriods,
                'year' => $year,
                'totalCountActiveUsers' => $this->getTotalCountActiveUsers($dateFrom, $dateTo),
                'admin_pool' => $this->container->get('sonata.admin.pool')
            )
        );
    }

    private function counting(&$stats, $countsPerMonth, $name)
    {
        foreach ($countsPerMonth as $countPerMonth) {
            if (preg_match('/(.*)_increment/ui', $name, $matches)) {
                $stats[$countPerMonth['_group_column']][$matches[1]] += (int)$countPerMonth['_count'];
            }
            $stats[$countPerMonth['_group_column']][$name] = (int)$countPerMonth['_count'];
        }
    }

    private function getTotalCountActiveUsers($dateFrom, $dateTo)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $countsUsers = (int)$em
            ->getRepository('MetalDemandsBundle:AbstractDemand')
            ->createQueryBuilder('ad')
            ->select('COUNT(DISTINCT ad.user) AS _count')
            ->andWhere('ad.createdAt BETWEEN :date_from AND :date_to')
            ->setParameter('date_from', $dateFrom)
            ->setParameter('date_to', $dateTo)
            ->getQuery()
            ->useResultCache(true, $this->cacheTtl)
            ->getSingleScalarResult();

        $countsAnonymousByEmail = (int)$em
            ->getRepository('MetalDemandsBundle:AbstractDemand')
            ->createQueryBuilder('ad')
            ->select('COUNT(DISTINCT ad.email) AS _count')
            ->where('ad.user IS NULL')
            ->andWhere('ad.createdAt BETWEEN :date_from AND :date_to')
            ->andWhere("ad.email <> ''")
            ->andWhere('ad.email IS NOT NULL')
            ->setParameter('date_from', $dateFrom)
            ->setParameter('date_to', $dateTo)
            ->getQuery()
            ->useResultCache(true, $this->cacheTtl)
            ->getSingleScalarResult();

        $countsAnonymousByPhone = (int)$em
            ->getRepository('MetalDemandsBundle:AbstractDemand')
            ->createQueryBuilder('ad')
            ->select('COUNT(DISTINCT ad.phone) AS _count')
            ->where('ad.user IS NULL')
            ->andWhere('ad.createdAt BETWEEN :date_from AND :date_to')
            ->andWhere('ad.email IS NULL')
            ->setParameter('date_from', $dateFrom)
            ->setParameter('date_to', $dateTo)
            ->getQuery()
            ->useResultCache(true, $this->cacheTtl)
            ->getSingleScalarResult();

        return $countsUsers + $countsAnonymousByEmail + $countsAnonymousByPhone;
    }
}
