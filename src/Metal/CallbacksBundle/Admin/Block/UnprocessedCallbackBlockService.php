<?php

namespace Metal\CallbacksBundle\Admin\Block;

use Doctrine\ORM\EntityManager;
use Metal\CallbacksBundle\Entity\Callback;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;

class UnprocessedCallbackBlockService extends AbstractAdminBlockService
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
        $unprocessedCallbacks = array();
        if ($isGranted = $this->adminPool->getAdminByAdminCode('metal.callbacks.admin.callback')->isGranted('LIST')) {
            $unprocessedCallbacks = $this->em->getRepository('MetalCallbacksBundle:Callback')
                ->createQueryBuilder('c')
                ->addSelect('category')
                ->addSelect('city')
                ->andWhere('c.processedBy IS NULL')
                ->andWhere('c.kind = :to_moderator')
                ->leftJoin('c.category', 'category')
                ->leftJoin('c.city', 'city')
                ->orderBy('c.createdAt', 'DESC')
                ->setParameter('to_moderator', Callback::CALLBACK_TO_MODERATOR)
                ->setMaxResults(UnprocessedCallbackBlockService::MAX_RESULTS)
                ->getQuery()
                ->getResult();
        }

        return $this->renderResponse('MetalCallbacksBundle:CallbackAdmin/Block:unprocessed_callback_block_service.html.twig', array(
            'block'     => $blockContext->getBlock(),
            'settings'  => $blockContext->getSettings(),
            'unprocessedCallbacks' => $unprocessedCallbacks,
            'isGranted' => $isGranted
        ), $response);
    }
}
