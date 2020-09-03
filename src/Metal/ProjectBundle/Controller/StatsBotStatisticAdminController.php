<?php

namespace Metal\ProjectBundle\Controller;

use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Controller\CoreController;
use Symfony\Component\HttpFoundation\Request;

class StatsBotStatisticAdminController extends CoreController
{
    const MAX_RESULT = 100;
    const MAX_DETAIL_RESULT = 50;

    private $cacheTtl = 3600;

    public function viewAction(Request $request)
    {
        $currentDate = $this->getCurrentDate($request);

        $qb = $this
            ->getQb($currentDate)
            ->setMaxResults(self::MAX_RESULT);

        $groupsBy = array(
            'for_user_agent' => 'se.userAgent',
            'for_ip' => 'se.ip',
        );

        list($prevDate, $nextDate) = $this->getDateRange($currentDate);

        return $this->render(
            '@MetalProject/Admin/StatsBotStatisticAdmin/view.html.twig',
            array(
                'statistics' => $this->getResult($request, $qb, $groupsBy),
                'currentDate' => $currentDate,
                'prevDate' => $prevDate,
                'nextDate' => $nextDate,
                'admin_pool' => $this->container->get('sonata.admin.pool'),
            )
        );
    }

    public function viewDetailAction(Request $request)
    {
        $currentDate = $this->getCurrentDate($request);

        $qb = $this
            ->getQb($currentDate)
            ->setMaxResults(self::MAX_DETAIL_RESULT);

        if (null !== $value = $request->query->get('ip')) {
            $qb->andWhere('se.ip = :value')
                ->setParameter('value', $value);
        } elseif (null !== $value = $request->query->get('user_agent')) {
            $qb->andWhere('se.userAgent = :value')
                ->setParameter('value', $value);
        } else {
            throw new \InvalidArgumentException('Require "ip" or "user_agent"');
        }

        $groupsBy = array(
            'for_user_agent' => 'se.userAgent',
            'for_ip' => 'se.ip',
            'for_city' => 'se.cityId',
            'for_category' => 'se.categoryId',
        );

        list($prevDate, $nextDate) = $this->getDateRange($currentDate);

        return $this->render(
            '@MetalProject/Admin/StatsBotStatisticAdmin/view_detail.html.twig',
            array(
                'statistics' => $this->getResult($request, $qb, $groupsBy),
                'currentDate' => $currentDate,
                'prevDate' => $prevDate,
                'nextDate' => $nextDate,
                'value' => $value,
                'admin_pool' => $this->container->get('sonata.admin.pool'),
            )
        );
    }

    private function getResult(Request $request, QueryBuilder $qb, array $groupsBy)
    {
        $noCache = $request->query->get('no_cache');
        $statistics = array();
        foreach ($groupsBy as $name => $groupBy) {
            $newQb = clone $qb;
            $subQb = $newQb
                ->addSelect($groupBy.' AS value')
                ->andWhere($groupBy.' IS NOT NULL')
                ->groupBy($groupBy)
                ->getQuery();

            if (!$noCache) {
                $subQb->useResultCache(true, $this->cacheTtl);
            }

            $statistics[$name] = $subQb->getArrayResult();
        }

        return $statistics;
    }

    /**
     * @param \DateTime $date
     * @return QueryBuilder
     */
    private function getQb(\DateTime $date)
    {
        return $this->getDoctrine()
            ->getManager()
            ->getRepository('MetalStatisticBundle:StatsElement')
            ->createQueryBuilder('se')
            ->select('COUNT(se.id) AS _count')
            ->where('se.dateCreatedAt = :date')
            ->setParameter('date', $date, 'date')
            ->orderBy('_count', 'DESC');
    }

    private function getDateRange(\DateTime $dateTime)
    {
        $prevDate = clone $dateTime;
        $prevDate->modify('-1 days')->modify('midnight');
        $nextDate = null;
        if ((new \DateTime())->modify('midnight') > $dateTime) {
            $nextDate = clone $dateTime;
            $nextDate->modify('+1 days')->modify('midnight');
        }

        return array($prevDate, $nextDate);
    }

    private function getCurrentDate(Request $request)
    {
        return (new \DateTime($request->query->get('date', (new \DateTime())->format('Y-m-d'))))->modify('midnight');
    }
}
