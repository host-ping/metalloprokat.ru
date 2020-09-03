<?php

namespace Metal\StatisticBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Metal\StatisticBundle\Entity\DTO;
use Metal\StatisticBundle\Repository\AbstractStatsRepository;
use Metal\StatisticBundle\ViewModel\StatsResultViewModel;

class StatisticReportBuilder
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param string $mode
     * @param \DateTime $dateFrom
     * @param \DateTime $dateTo
     * @param array $criteria
     * @param string $group
     * @param string $order
     * @param string $direction
     *
     * @return array
     */
    public function getStatsResults($mode, \DateTime $dateFrom, \DateTime $dateTo, array $criteria = array(), $group = null, $order = null, $direction = null) {

        $config = $this->getConfigurationForStats($mode);
        $repo = $this->em->getRepository($config['repo']);
        /* @var $repo AbstractStatsRepository */

        $stats = $repo->getStatsResults($config['dto'], $dateFrom, $dateTo, $criteria, $group, $order, $direction);

        return array(
            'template' => $config['template'],
            'statsResultViewModel' => new StatsResultViewModel(
                $stats,
                $dateFrom,
                $dateTo,
                $config['linkEntries']
            )
        );
    }

    protected function getConfigurationForStats($mode)
    {
        $statsConfigs = array(
            'income_daily' => array(
                'dto' => new DTO\IncomingStatsResult(),
                'repo' => 'MetalStatisticBundle:StatsDaily',
                'template' => '@MetalPrivateOffice/PrivateStatistic/viewIncome.html.twig',
                'linkEntries' => true,
            ),
            'income_region' => array(
                'dto' => new DTO\IncomingByRegionStatsResult(),
                'repo' => 'MetalStatisticBundle:StatsCity',
                'template' => '@MetalPrivateOffice/PrivateStatistic/viewIncomeRegions.html.twig',
                'linkEntries' => false,
            ),
            'income_category' => array(
                'dto' => new DTO\IncomingByCategoryStatsResult(),
                'repo' => 'MetalStatisticBundle:StatsCategory',
                'template' => '@MetalPrivateOffice/PrivateStatistic/viewIncomeCategory.html.twig',
                'linkEntries' => false,
            ),
            'demand_daily' => array(
                'dto' => new DTO\DemandsStatsResult(),
                'repo' => 'MetalStatisticBundle:StatsDaily',
                'template' => '@MetalPrivateOffice/PrivateStatistic/viewDemand.html.twig',
                'linkEntries' => true,
            ),
            'demand_region' => array(
                'dto' => new DTO\DemandsByRegionStatsResult(),
                'repo' => 'MetalStatisticBundle:StatsCity',
                'template' => '@MetalPrivateOffice/PrivateStatistic/viewDemandRegion.html.twig',
                'linkEntries' => true,
            ),
            'demand_category' => array(
                'dto' => new DTO\DemandsByCategoryStatsResult(),
                'repo' => 'MetalStatisticBundle:StatsCategory',
                'template' => '@MetalPrivateOffice/PrivateStatistic/viewDemandCategory.html.twig',
                'linkEntries' => true,
            ),
            'management' => array(
                'dto' => new DTO\ManagementStatsResult(),
                'repo' => 'MetalStatisticBundle:StatsDaily',
                'template' => '@MetalPrivateOffice/PrivateStatistic/viewManagement.html.twig',
                'linkEntries' => true,
            ),
            'announcement' => array(
                'dto' => new DTO\AnnouncementStatsResult(),
                'repo' => 'MetalStatisticBundle:StatsAnnouncementDaily',
                'template' => '@MetalPrivateOffice/PrivateStatistic/viewMedia.html.twig',
                'linkEntries' => true,
            ),
        );

        $statsConfig = $statsConfigs[$mode];

        return $statsConfig;
    }
}
