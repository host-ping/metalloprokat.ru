<?php

namespace Metal\ProjectBundle\Admin\Block;

use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;

class StatisticBlockService extends AbstractAdminBlockService
{
    const FOR_DAYS = 30;

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
        if (!$this->adminPool->getAdminByAdminCode('metal.users.admin.user')->isGranted('LIST')) {
            return $this->renderResponse(
                'MetalProjectBundle:Admin/Block:statistic_block_service.html.twig',
                array(
                    'block' => $blockContext->getBlock(),
                    'settings' => $blockContext->getSettings(),
                    'data' => array(),
                    'isGranted' => false
                ),
                $response
            );
        }
        $users = $this->normalizeRows(
            $conn->fetchAll(
                '
            SELECT
                DATE(Created) AS created_date,
                COUNT(*) AS users_total_count,
                COUNT(IF(Confirmed = 0, NULL, 1)) AS users_confirmed_count
                FROM User
                WHERE Created >= :date_from
                GROUP BY created_date
            ',
                array(
                    'date_from' => $dateFrom->format('Y-m-d')
                )
            )
        );

        $companies = $this->normalizeRows(
            $conn->fetchAll(
                'SELECT DATE(company.Created) AS created_date, COUNT(*) AS companies_total_count
                FROM Message75 AS company
              WHERE company.Created >= :date_from
              GROUP BY created_date',
                array(
                    'date_from' => $dateFrom->format('Y-m-d')
                )
            )
        );

        $companiesFromYandexDirect = $this->normalizeRows(
            $conn->fetchAll(
                "SELECT DATE(company.Created) AS created_date, COUNT(*) AS companies_ya_direct_total_count, COUNT(p.package_order_id) AS countPayments
                FROM Message75 AS company
                  JOIN company_log AS companyLog ON companyLog.company_id = company.Message_ID
                  JOIN User AS user ON user.User_ID = companyLog.created_by
                  LEFT JOIN Payments p ON company.Message_ID = p.Company_ID
                  WHERE company.Created >= :date_from
                  AND
                  user.referer LIKE '%yandexdirect%'
                GROUP BY created_date",
                array(
                    'date_from' => $dateFrom->format('Y-m-d')
                )
            )
        );

        $dateStart = date('Y-m-d');
        $dateEnd = date("Y-m-d", time() - 30 * (24 * 60 * 60));
        $data = array();
        $i = StatisticBlockService::FOR_DAYS;
        while ($dateStart >= $dateEnd) {
            $data[$dateEnd] = array_merge(
                array('date' => new \DateTime($dateEnd)),
                isset($users[$dateEnd]) ? $users[$dateEnd] : array(),
                isset($companies[$dateEnd]) ? $companies[$dateEnd] : array(),
                isset($companiesFromYandexDirect[$dateEnd]) ? $companiesFromYandexDirect[$dateEnd] : array()
            );

            $dateEnd = date("Y-m-d", time() - $i * (24 * 60 * 60));
            $i--;
        }
        $data = array_reverse($data);

        return $this->renderResponse(
            'MetalProjectBundle:Admin/Block:statistic_block_service.html.twig',
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'data' => $data,
                'isGranted' => true
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
            $normalizedData[$row['created_date']] = $row;
        }

        return $normalizedData;
    }
}
