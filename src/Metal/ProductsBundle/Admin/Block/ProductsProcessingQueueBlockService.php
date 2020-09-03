<?php

namespace Metal\ProductsBundle\Admin\Block;

use Doctrine\ORM\EntityManager;
use Metal\ProductsBundle\Entity\Product;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;

class ProductsProcessingQueueBlockService extends AbstractAdminBlockService
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
        $productsProcessingQueue = array();
        if ($isGranted = $this->adminPool->getAdminByAdminCode('metal.products.admin.product')->isGranted('LIST')) {
            $productsProcessingQueue = $this->em->getConnection()
                ->createQueryBuilder()
                ->from('Message142', 'p')
                ->select('COUNT(IF(p.Checked = :waiting_moderation, 1, NULL)) AS waiting_moderation')
                ->setParameter('waiting_moderation', Product::STATUS_NOT_CHECKED)
                ->addSelect('COUNT(IF(p.Checked = :waiting_category_detection, 1, NULL)) AS waiting_category_detection')
                ->setParameter('waiting_category_detection', Product::STATUS_PENDING_CATEGORY_DETECTION)
                ->addSelect('COUNT(IF(p.Checked = :in_processing, 1, NULL)) AS in_processing')
                ->setParameter('in_processing', Product::STATUS_PROCESSING)
                ->execute()
                ->fetch()
            ;
        }

        return $this->renderResponse(
            'MetalProductsBundle:ProductAdmin/Block:products_proccesing_queue_block_service.html.twig',
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'productsProcessingQueue' => $productsProcessingQueue,
                'isGranted' => $isGranted
            ),
            $response
        );
    }
}
