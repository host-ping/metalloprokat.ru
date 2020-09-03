<?php

namespace Metal\GrabbersBundle\Admin\Block;

use Doctrine\ORM\EntityManager;
use Metal\GrabbersBundle\Entity\Site;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;

class SiteStatisticBlockService extends AbstractAdminBlockService
{
    const FOR_DAYS = 5;

    /**
     * @var Site[]
     */
    protected $enabledSites = array();

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
        $isGranted = $this->adminPool->getAdminByAdminCode('metal.grabbers.admin.parsed_demand')->isGranted('LIST');
        $result = array();

        if ($isGranted) {
            $parsedDemands = $this->em->getRepository('MetalGrabbersBundle:ParsedDemand')
                ->createQueryBuilder('ParsedDemand')
                ->select('IDENTITY(ParsedDemand.site) AS siteId')
                ->addSelect('ParsedDemand.createdAt AS createdAt')
                ->addSelect('demand.moderatedAt AS demandModeratedAt')
                ->addSelect('demand.deletedAt AS demandDeletedAt')
                ->join('ParsedDemand.demand', 'demand')
                ->andWhere('ParsedDemand.createdAt >= :days')
                ->orderBy('ParsedDemand.createdAt', 'ASC')
                ->setParameter('days', (new \DateTime(sprintf('-%d day', self::FOR_DAYS)))->format('Y-m-d'))
                ->getQuery()
                ->getArrayResult();


            $statistics = array();
            foreach ($parsedDemands as $parsedDemand) {
                if (!isset($statistics[$parsedDemand['siteId']][$parsedDemand['createdAt']->format('Y-m-d')])) {
                    $statistics[$parsedDemand['siteId']][$parsedDemand['createdAt']->format('Y-m-d')] = array();
                }

                $statistics[$parsedDemand['siteId']][$parsedDemand['createdAt']->format('Y-m-d')][] = $parsedDemand;
            }

            $this->enabledSites = $this->em
                ->getRepository('MetalGrabbersBundle:Site')
                ->findBy(array('isEnabled' => true));

            $result = $this->createEmptyList();

            for ($i = 0; $i < self::FOR_DAYS; $i++) {
                $date = new \DateTime(sprintf('-%d day', $i));

                foreach ($this->enabledSites as $enabledSite) {

                    if (!isset($statistics[$enabledSite->getId()][$date->format('Y-m-d')])) {
                        continue;
                    }

                    foreach ((array)$statistics[$enabledSite->getId()][$date->format('Y-m-d')] as $row) {

                        $result[$enabledSite->getId()]['counters'][$date->format('Y-m-d')]['total_parsed_demands']++;

                        if (null !== $row['demandModeratedAt']) {
                            $result[$enabledSite->getId()]['counters'][$date->format('Y-m-d')]['total_moderated_demands']++;
                        }

                        if (null !== $row['demandModeratedAt'] || null !== $row['demandDeletedAt']) {
                            $result[$enabledSite->getId()]['counters'][$date->format('Y-m-d')]['total_processed_demands']++;
                        }
                    }
                }
            }
        }

        return $this->renderResponse(
            'MetalGrabbersBundle:SiteAdmin/Block:site_statistic_block_service.html.twig',
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'isGranted' => $isGranted,
                'statistics' => $result
            ),
            $response
        );
    }

    public function createEmptyList()
    {
        $list = array();
        for ($i = 0; $i < self::FOR_DAYS; $i++) {
            $date = new \DateTime(sprintf('-%d days', $i));
            foreach ($this->enabledSites as $key => $enabledSite) {

                if (!isset($list[$enabledSite->getId()]['site_info'])) {
                    $list[$enabledSite->getId()]['site_info'] = $enabledSite;
                }

                $list[$enabledSite->getId()]['counters'][$date->format('Y-m-d')] = array(
                    'total_parsed_demands' => 0,
                    'total_moderated_demands' => 0,
                    'total_processed_demands' => 0,
                    'date' => $date
                );
            }
        }

        return $list;
    }

}
