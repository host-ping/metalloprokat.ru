<?php

namespace Metal\DemandsBundle\Admin\Block;

use Doctrine\ORM\EntityManager;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\ProjectBundle\Entity\ValueObject\AdminSourceTypeProvider;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;

class SourceDemandBlockService extends AbstractAdminBlockService
{
    const FOR_DAYS = 10;

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
        $dateFrom = new \DateTime(sprintf('-%d days', self::FOR_DAYS));
        $demands = array();

        if ($isGranted = $this->adminPool->getAdminByAdminCode('metal.demands.admin.demand')->isGranted('LIST')) {
            $demandsCreatedData = $this->em->getConnection()->fetchAll('
                SELECT COUNT(d.id) AS _count_created, d.admin_source_type AS source, DATE(d.created_at) AS date FROM demand d
                WHERE d.moderated_at IS NOT NULL
                AND d.demand_type = :demand_type
                AND d.created_at >= :date_from
                GROUP BY d.admin_source_type, DATE(d.created_at)
                ORDER BY DATE(d.created_at) DESC
            ', array('date_from' => $dateFrom, 'demand_type' => AbstractDemand::TYPE_PUBLIC), array('date_from' => 'date'));

            $demandsModeratedData = $this->em->getConnection()->fetchAll('
                SELECT COUNT(d.id) AS _count_moderated, d.admin_source_type AS source, DATE(d.moderated_at) AS date FROM demand d
                WHERE d.moderated_at IS NOT NULL
                AND d.demand_type = :demand_type
                AND d.moderated_at >= :date_from
                GROUP BY d.admin_source_type, DATE(d.moderated_at)
                ORDER BY DATE(d.moderated_at) DESC
            ', array('date_from' => $dateFrom, 'demand_type' => AbstractDemand::TYPE_PUBLIC), array('date_from' => 'date'));


            $demands = $this->createEmptyList();

            foreach ($demandsCreatedData as $demandCreatedData) {
                $dateCreated = new \DateTime($demandCreatedData['date']);
                $demands[$dateCreated->format('d.m.Y')][$demandCreatedData['source']]['_count_created'] = $demandCreatedData['_count_created'];
            }

            foreach ($demandsModeratedData as $demandModeratedData) {
                $dateModereted = new \DateTime($demandModeratedData['date']);
                $demands[$dateModereted->format('d.m.Y')][$demandModeratedData['source']]['_count_moderated'] = $demandModeratedData['_count_moderated'];
            }
        }

        return $this->renderResponse(
            'MetalDemandsBundle:DemandAdmin/Block:source_demand_block_service.html.twig',
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'demands' => $demands,
                'isGranted' => $isGranted,
            ),
            $response
        );
    }

    public function createEmptyList()
    {
        $list = array();
        $allTypesAsSimpleArray = AdminSourceTypeProvider::getAllTypesAsSimpleArray();
        for ($i = 0; $i <= self::FOR_DAYS; $i++) {
            $date = new \DateTime(sprintf('-%d days', $i));
            foreach ($allTypesAsSimpleArray as $key => $typeTitle) {
                $list[$date->format('d.m.Y')][$key] = array(
                    'source_title' => $typeTitle,
                    'date' => $date,
                    '_count_moderated' => 0,
                    '_count_created' => 0,
                );
            }
        }

        return $list;
    }
}
