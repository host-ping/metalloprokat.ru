<?php

namespace Metal\UsersBundle\Admin\Block;

use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;

class WizzardRegisterBlockService extends AbstractAdminBlockService
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
        $dateFrom = new \DateTime('-1 month');
        $dateTo = new \DateTime();
        $conn = $this->em->getConnection();

        $data = array();
        if ($isGranted = $this->adminPool->getAdminByAdminCode('metal.users.admin.user')->isGranted('LIST')) {
            $data = $this->normalizeRows($conn->fetchAll("
            SELECT DATE(cr.created_at) AS created_date, c.cat_name AS category, COUNT(cr.company_id) AS count, COUNT(p.package_order_id) AS countPayments
            FROM company_registration AS cr
            JOIN Message73 AS c ON cr.category_id = c.Message_ID
            LEFT JOIN Message75 company ON company.Message_ID = cr.company_id
            LEFT JOIN Payments p ON company.Message_ID = p.Company_ID
            WHERE DATE(cr.created_at) BETWEEN :start_date AND :end_date
            GROUP BY cr.category_id, created_date
            ORDER BY created_date DESC
            ",
                array(
                    'start_date' => $dateFrom->format('Y-m-d'),
                    'end_date' => $dateTo->format('Y-m-d')
                )
            ));
        }


        return $this->renderResponse(
            'MetalUsersBundle:UserAdmin/Block:wizzard_register_block_service.html.twig',
            array(
                'data' => $data,
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
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
            $normalizedData[$row['created_date']][] = array(
                'created_date' => new \DateTime($row['created_date']),
                'category' => $row['category'],
                'count' => $row['count'],
                'countPayments' => $row['countPayments']

            );
        }

        return $normalizedData;
    }

}
