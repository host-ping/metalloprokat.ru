<?php

namespace Metal\ProductsBundle\Consumer;

use Brouzie\Components\Indexer\Indexer;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;

class ProductsImportConsumer implements ConsumerInterface
{
    protected $em;

    protected $indexer;

    public function __construct(EntityManagerInterface $em, Indexer $indexer)
    {
        $this->em = $em;
        $this->indexer = $indexer;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ConsumerEvent $event)
    {
        $eventData = $event->getMessage()->getBody();

        if (!empty($eventData['insert_product_changes'])) {
            $statsProductChangesRepository = $this->em->getRepository('MetalStatisticBundle:StatsProductChange');

            foreach ($eventData['insert_product_changes'] as $productChange) {
                $statsProductChangesRepository
                    ->insertProductChanges(
                        $productChange['company_id'],
                        $productChange['product_id'],
                        $productChange['is_added']
                    );
            }
        }

        if (!empty($eventData['products_reindex_ids'])) {
            $this->indexer->reindexIds($eventData['products_reindex_ids']);
        }
    }
}
