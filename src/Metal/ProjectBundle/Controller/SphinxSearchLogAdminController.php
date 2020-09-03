<?php

namespace Metal\ProjectBundle\Controller;

use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;

class SphinxSearchLogAdminController extends CoreController
{
    private $cacheTtl = 18000;

    const MAX_RESULT = 50;

    public function viewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $qb = $em
            ->getRepository('MetalProjectBundle:SphinxSearchLog')
            ->createQueryBuilder('sphinxSearchLog')
            ->select('COUNT(sphinxSearchLog) AS _count')
            ->addSelect('SUM(sphinxSearchLog.timeReal) AS sum_time')
            ->addSelect('AVG(sphinxSearchLog.timeReal) AS tpq')
            ->addSelect('MAX(sphinxSearchLog.timeReal) AS max_time')
            ->addSelect('sphinxSearchLog.rawQuery AS raw_query')
            ->groupBy('sphinxSearchLog.queryHash')
            ->setMaxResults(self::MAX_RESULT);

        $statQb = $em
            ->getRepository('MetalProjectBundle:SphinxSearchLog')
            ->createQueryBuilder('sphinxSearchLog')
            ->select('COUNT(sphinxSearchLog) AS _count')
            ->addSelect('MIN(sphinxSearchLog.createdAt) AS min_date')
            ->addSelect('MAX(sphinxSearchLog.createdAt) AS max_date');

        if ($queriesCount = $request->query->get('queries-count', 1)) {
            $qb->having('_count >= :queries_count')
                ->setParameter('queries_count', $queriesCount)
            ;

            $qb->having('_count >= :queries_count')
                ->setParameter('queries_count', $queriesCount)
            ;
        }

        if ($request->query->get('for-day')) {
            $prevDay = new \DateTime('-1 day');
            $qb->andWhere('sphinxSearchLog.createdAt >= :prev_day')
                ->setParameter('prev_day', $prevDay)
            ;

            $statQb->andWhere('sphinxSearchLog.createdAt >= :prev_day')
                ->setParameter('prev_day', $prevDay)
            ;
        }

        $groupBy = array(
            '_count' => 'DESC',
            'max_time' => 'DESC',
            'sum_time' => 'DESC',
            'tpq' => 'DESC',
        );

        $statistics = array();
        foreach ($groupBy as $key => $order) {
            $newQb = clone $qb;
            $statistics[] = $newQb
                ->orderBy($key, $order)
                ->getQuery()
                ->useResultCache(true, $this->cacheTtl)
                ->getArrayResult();
        }

        $headerStat = $statQb->getQuery()->useResultCache(true, $this->cacheTtl)->setMaxResults(1)->getOneOrNullResult();
        if ($headerStat) {
            $seconds = (new \DateTime($headerStat['max_date']))->getTimestamp() - (new \DateTime($headerStat['min_date']))->getTimestamp();
            if (!$seconds) {
                $headerStat['qps'] = $headerStat['_count'];
            } else {
                $headerStat['qps'] = $headerStat['_count'] / $seconds;
            }
        }

        return $this->render(
            '@MetalProject/Admin/SphinxSearchLogAdmin/view.html.twig',
            array(
                'header_stat' => $headerStat,
                'statistics' => $statistics,
                'admin_pool' => $this->container->get('sonata.admin.pool'),
            )
        );
    }

}
