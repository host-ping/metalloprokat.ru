<?php

namespace Metal\CatalogBundle\Admin\Block;

use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;

class AddedProductBlockService extends AbstractAdminBlockService
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
        $conn = $this->em->getConnection();
        $dateFrom = new \DateTime('-1 month');
        $productsByUsers = array();
        $catalogEnabled = $this->adminPool->getContainer()->getParameter('project.catalog_enabled');
        $isGranted = $catalogEnabled && $this->adminPool->getAdminByAdminCode('metal.catalog.admin.product')->isGranted('LIST');
        if ($isGranted) {
            $productsByUsers = $this->normalizeRows(
                $conn->fetchAll('
                    SELECT
                        DATE(cp.created_at) AS created_date,
                        COUNT(cp.id) AS product_total_count,
                        cp.created_by AS user_id,
                        CONCAT_WS(" ", u.ForumName, u.LastName) AS fullName
                    FROM catalog_product cp
                    JOIN User u ON cp.created_by = u.User_ID
                        AND cp.created_at >= :date_from
                    GROUP BY created_date, user_id
                    ORDER BY created_date DESC
                    ',
                    array(
                        'date_from' => $dateFrom->format('Y-m-d')
                    )
                )
            );
        }

        return $this->renderResponse(
            'MetalCatalogBundle:AdminProduct/Block:added_products_block.html.twig',
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'data' => $productsByUsers,
                'isGranted' => $isGranted
            ),
            $response
        );
    }

    /**
     * @param array $rows
     * @return array
     */
    private function normalizeRows(array $rows = array())
    {
        $normalizedData = array();
        foreach ($rows as $row) {
            $normalizedData[$row['created_date']][] = $row;
        }

        return $normalizedData;
    }
}
