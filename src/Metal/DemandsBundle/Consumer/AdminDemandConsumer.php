<?php

namespace Metal\DemandsBundle\Consumer;

use Brouzie\Sphinxy\IndexManager;
use Doctrine\ORM\EntityManager;
use Metal\DemandsBundle\ChangeSet\DemandsBatchEditChangeSet;
use Metal\ProjectBundle\Util\InsertUtil;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;

class AdminDemandConsumer implements ConsumerInterface
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var IndexManager
     */
    protected $indexManager;

    public function __construct(EntityManager $em, IndexManager $indexManager)
    {
        $this->em = $em;
        $this->indexManager = $indexManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ConsumerEvent $event)
    {
        $demandData = $event->getMessage()->getBody();

        if (isset($demandData['changeset'])) {
            $demandsChangeSet = $demandData['changeset'];
            /* @var $demandsChangeSet DemandsBatchEditChangeSet */
            if ($demandsChangeSet->demandItemsToChangeAttributesValues) {
                $demandItemAttributeValueRepository = $this->em->getRepository('MetalDemandsBundle:DemandItemAttributeValue');
                $callable = function ($demandsItemsIds) use ($demandItemAttributeValueRepository) {
                    $demandItemAttributeValueRepository->changeAttributeValues($demandsItemsIds);
                };

                InsertUtil::processBatch($demandsChangeSet->demandItemsToChangeAttributesValues, $callable, 500);
            }
        }

        if (isset($demandData['reindex'])) {
            $this->indexManager->reindexItems('demands', $demandData['reindex']);
        }

        if (isset($demandData['removeIndex'])) {
            $this->indexManager->removeItems('demands', $demandData['removeIndex']);
        }
    }
}
