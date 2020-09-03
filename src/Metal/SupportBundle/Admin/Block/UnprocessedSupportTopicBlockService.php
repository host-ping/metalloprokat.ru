<?php

namespace Metal\SupportBundle\Admin\Block;

use Doctrine\ORM\EntityManager;
use Metal\SupportBundle\Entity\Topic;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;

class UnprocessedSupportTopicBlockService extends AbstractAdminBlockService
{
    const MAX_RESULTS = 10;

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
        $unprocessedTopics = array();
        if ($isGranted = $this->adminPool->getAdminByAdminCode('metal.support.admin.topic')->isGranted('LIST')) {
            $unprocessedTopics = $this->em->getRepository('MetalSupportBundle:Topic')
                ->createQueryBuilder('topic')
                ->select('topic')
                ->addSelect('user')
                ->addSelect('company')
                ->where('topic.resolvedAt IS NULL')
                ->leftJoin('topic.author', 'user')
                ->leftJoin('topic.company', 'company')
                ->orderBy('topic.lastAnswerAt', 'DESC')
                ->setMaxResults(self::MAX_RESULTS)
                ->getQuery()
                ->getResult();
        }

        return $this->renderResponse(
            'MetalSupportBundle:TopicAdmin/Block:unprocessed_support_topics_block_service.html.twig',
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'unprocessedTopics' => $unprocessedTopics,
                'isGranted' => $isGranted
            ),
            $response
        );
    }
}
