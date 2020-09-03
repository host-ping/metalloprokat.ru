<?php

namespace Metal\ServicesBundle\Admin\Block;

use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;

class UnprocessedPackageOrderBlockService extends AbstractAdminBlockService
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
        $unprocessedServiceOrder = array();
        if ($isGranted = $this->adminPool->getAdminByAdminCode('metal.services.admin.package_order')->isGranted('LIST')) {
            $unprocessedServiceOrder = $this->em->getRepository('MetalServicesBundle:PackageOrder')
                ->createQueryBuilder('package')
                ->addSelect('company')
                ->leftJoin('package.company', 'company')
                ->where('package.processedBy IS NULL')
                ->orderBy('package.createdAt', 'DESC')
                ->setMaxResults(self::MAX_RESULTS)
                ->getQuery()
                ->getResult();
        }

        return $this->renderResponse(
            'MetalServicesBundle:Admin/Block:unprocessed_package_order_block_service.html.twig',
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'unprocessedServiceOrders' => $unprocessedServiceOrder,
                'isGranted' => $isGranted
            ), $response
        );
    }
}
