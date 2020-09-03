<?php

namespace Metal\ProjectBundle\Admin\Block;

use Doctrine\ORM\EntityManager;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class StatisticSearchBlockService extends AbstractAdminBlockService
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setAuthorizationChecker(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $conn = $this->em->getConnection();
        $stats = array();

        $isGranted = $this->authorizationChecker->isGranted('ROLE_MANAGER');
        if ($isGranted) {
            $sql = 'SELECT
                COUNT(IF(kind = 1, 1, NULL)) AS suggest_search,
                COUNT(IF(kind = 2, 1, NULL)) AS button_search
            FROM stats_search_kind
            WHERE created_at >= :date_from
            ';

            $periods = array(
                'day' => new \DateTime('-1 day'),
                'week' => new \DateTime('-1 week'),
                'month' => new \DateTime('-1 month'),
                'year' => new \DateTime('-1 year'),
            );

            foreach ($periods as $key => $period) {
                $stats[$key] = $conn->fetchAssoc($sql, array('date_from' => $period->format('Y-m-d')));
            }
        }

        return $this->renderResponse(
            'MetalProjectBundle:Admin/Block:statistic_search_block_service.html.twig',
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'stats' => $stats,
                'isGranted' => $isGranted
            ),
            $response
        );
    }
}
