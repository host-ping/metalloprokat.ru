<?php
namespace Metal\NewsletterBundle\Admin\Block;

use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;

class NewsletterStatisticBlockService extends AbstractAdminBlockService
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
        $result = array();
        if ($isGranted = $this->adminPool->getAdminByAdminCode('metal.newsletter.admin.newsletter')->isGranted('LIST')) {
            $qb = $this->em
                ->getRepository('MetalNewsletterBundle:Subscriber')
                ->createQueryBuilder('subscriber')
                ->select('COUNT(subscriber.id)')
                ->setParameter('dateFrom', new \DateTime('today'));

            $result = array(
                'demandsDigest' => array(
                    'count' => $qb->where('subscriber.demandsDigestSentAt >= :dateFrom')
                        ->getQuery()
                        ->getSingleScalarResult(),
                    'label' => 'Ежедневный дайджест заявок'
                ),
                'recallEmail' => array(
                    'count' => $qb->where('subscriber.recallEmailSentAt >= :dateFrom')
                        ->getQuery()
                        ->getSingleScalarResult(),
                    'label' => 'Напоминание актуализации цены'
                ),
                'priceInviteEmail' => array(
                    'count' => $qb->where('subscriber.priceInviteEmailSentAt >= :dateFrom')
                        ->getQuery()
                        ->getSingleScalarResult(),
                    'label' => 'Предложение привлечь больше клиентов'
                ),
                'demandRecallEmail' => array(
                    'count' => $qb->where('subscriber.demandRecallEmailSentAt >= :dateFrom')
                        ->getQuery()
                        ->getSingleScalarResult(),
                    'label' => 'Повторное письмо, оставившим заявку'
                ),
                'recipientSent' => array(
                    'count' => $qb->join('MetalNewsletterBundle:Recipient', 'r', 'WITH', 'subscriber.id = r.subscriber')
                        ->where('r.sentAt >= :dateFrom')
                        ->getQuery()
                        ->getSingleScalarResult(),
                    'label' => 'Рассылки'
                )
            );
        }

        return $this->renderResponse(
            '@MetalNewsletter/SubscriberAdmin/Block/newsletter_statistic_block_service.html.twig',
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'result' => $result,
                'isGranted' => $isGranted
            ),
            $response
        );
    }
}
