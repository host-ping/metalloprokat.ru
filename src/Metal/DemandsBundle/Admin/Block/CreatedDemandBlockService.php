<?php

namespace Metal\DemandsBundle\Admin\Block;

use Doctrine\ORM\EntityManager;
use Metal\ProjectBundle\Entity\ValueObject\SiteSourceTypeProvider;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;

class CreatedDemandBlockService extends AbstractAdminBlockService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Pool
     */
    private $adminPool;

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setAdminPool(Pool $adminPool)
    {
        $this->adminPool = $adminPool;
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $dateHelper = $this->adminPool->getContainer()->get('brouzie.helper_factory')->get('MetalProjectBundle:Formatting');
        $prevMonth = $dateHelper->getMonthLocalized(new \DateTime('first day of previous month'), 'normal');
        $currentMonth = $dateHelper->getMonthLocalized(new \DateTime('now'), 'normal');
        $currentMonthDemands = array();
        $previousMonthDemands = array();

        $isGranted = $this->adminPool->getAdminByAdminCode('metal.demands.admin.demand')->isGranted('LIST');

        if ($isGranted) {
            $qb = $this->em->getRepository('MetalDemandsBundle:Demand')
                ->createQueryBuilder('d')
                ->select('COUNT(d) AS demandsCount')
                ->addSelect('IDENTITY(d.user) AS userId')
                ->addSelect('d.createdAt AS createdAt')
                ->andWhere('d.sourceTypeId = :source')
                ->andWhere('d.moderatedAt IS NOT NULL')
                ->andWhere('d.user IS NOT NULL')
                ->andWhere('d.createdAt BETWEEN :from_day AND :to_day')
                ->groupBy('d.user')
                ->setParameter('source', SiteSourceTypeProvider::SOURCE_ADMIN);

            $previousMonthFrom = new \DateTime('first day of previous month midnight');
            $previousMonthTo = new \DateTime('first day of this month midnight -1 second');
            $previousMonthDemands = $qb
                ->setParameter('from_day', $previousMonthFrom)
                ->setParameter('to_day', $previousMonthTo)
                ->getQuery()
                ->getResult();

            $currentMonthFrom = new \DateTime('first day of this month midnight');
            $currentMonthTo = new \DateTime('now');
            $currentMonthDemands = $qb
                ->setParameter('from_day', $currentMonthFrom)
                ->setParameter('to_day', $currentMonthTo)
                ->getQuery()
                ->getResult();

            $userIds = array();
            foreach ($previousMonthDemands as $demand) {
                $userIds[$demand['userId']] = true;
            }

            foreach ($currentMonthDemands as $demand) {
                $userIds[$demand['userId']] = true;
            }

            $users = $this->em->createQueryBuilder()
                ->select('user')
                ->from('MetalUsersBundle:User', 'user', 'user.id')
                ->where('user.id IN (:usersIds)')
                ->setParameter('usersIds', array_keys($userIds))
                ->getQuery()
                ->getResult();

            $previousMonthTo->modify('+1 day');
            foreach ($previousMonthDemands as $key => $demand) {
                $previousMonthDemands[$key]['user'] = $users[$demand['userId']];
                $previousMonthDemands[$key]['dateFrom'] = $previousMonthFrom->format('d.m.Y');
                $previousMonthDemands[$key]['dateTo'] = $previousMonthTo->format('d.m.Y');
            }

            $currentMonthTo->modify('+1 day');
            foreach ($currentMonthDemands as $key => $demand) {
                $currentMonthDemands[$key]['user'] = $users[$demand['userId']];
                $currentMonthDemands[$key]['dateFrom'] = $currentMonthFrom->format('d.m.Y');
                $currentMonthDemands[$key]['dateTo'] = $currentMonthTo->format('d.m.Y');
            }

            $sorter = function ($a, $b) {
                return strcmp($a['user']->getFullName(), $b['user']->getFullName());
            };

            usort($previousMonthDemands, $sorter);
            usort($currentMonthDemands, $sorter);
        }

        return $this->renderResponse(
            'MetalDemandsBundle:DemandAdmin/Block:created_demand_block_service.html.twig',
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'currentMonthDemands' => $currentMonthDemands,
                'previousMonthDemands' => $previousMonthDemands,
                'isGranted' => $isGranted,
                'prevMonth' => $prevMonth,
                'currMonth' => $currentMonth
            ),
            $response
        );
    }
}
