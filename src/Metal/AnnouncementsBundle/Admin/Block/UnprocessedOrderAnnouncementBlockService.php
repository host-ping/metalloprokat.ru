<?php

namespace Metal\AnnouncementsBundle\Admin\Block;

use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;

class UnprocessedOrderAnnouncementBlockService extends AbstractAdminBlockService
{
    const MAX_RESULTS = 5;

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
        $unprocessedOrderAnnouncements = array();
        if ($isGranted = $this->adminPool->getAdminByAdminCode('metal.announcements.admin.order_announcement')->isGranted('LIST')) {
            $unprocessedOrderAnnouncements = $this->em->getRepository('MetalAnnouncementsBundle:OrderAnnouncement')
                ->createQueryBuilder('orderAnnouncement')
                ->select('orderAnnouncement')
                ->addSelect('zone')
                ->addSelect('user')
                ->where('orderAnnouncement.processedAt is NULL')
                ->leftJoin('orderAnnouncement.zone', 'zone')
                ->leftJoin('orderAnnouncement.user', 'user')
                ->orderBy('orderAnnouncement.createdAt', 'DESC')
                ->setMaxResults(self::MAX_RESULTS)
                ->getQuery()
                ->getResult();
        }

        return $this->renderResponse(
            'MetalAnnouncementsBundle:OrderAnnouncementAdmin/Block:unprocessed_block_service.html.twig',
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'unprocessedOrderAnnouncements' => $unprocessedOrderAnnouncements,
                'isGranted' => $isGranted
            ), $response
        );
    }
}
