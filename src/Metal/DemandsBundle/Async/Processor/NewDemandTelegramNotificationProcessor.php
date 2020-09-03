<?php

namespace Metal\DemandsBundle\Async\Processor;

use Enqueue\Client\TopicSubscriberInterface;
use Enqueue\Consumption\QueueSubscriberInterface;
use Enqueue\Consumption\Result;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;
use Interop\Queue\PsrProcessor;
use Metal\DemandsBundle\Async\Events;
use Metal\DemandsBundle\Async\Message\DemandModerated;
use Metal\DemandsBundle\Notifications\Notifier;

class NewDemandTelegramNotificationProcessor implements PsrProcessor, TopicSubscriberInterface, QueueSubscriberInterface
{
    private $notifier;

    private $enabled;

    public function __construct(Notifier $notifier, bool $enabled)
    {
        $this->notifier = $notifier;
        $this->enabled = $enabled;
    }

    public function process(PsrMessage $message, PsrContext $context)
    {
        if (!$this->enabled) {
            return self::ACK;
        }

        try {
            /** @var DemandModerated $event */
            $event = DemandModerated::jsonDeserialize($message->getBody());
        } catch (\Exception $e) {
            return Result::reject($e->getMessage());
        }

        $this->notifier->notifyOnNewDemand($event->getDemandId());

        return self::ACK;
    }

    public static function getSubscribedTopics()
    {
        return [
            Events::EVENT_DEMAND_MODERATED => [
                'processorName' => 'demand.demand_moderated_notification',
                'queueName' => Events::QUEUE_NAME_DEMANDS,
                'queueNameHardcoded' => true,
            ],
        ];
    }

    public static function getSubscribedQueues()
    {
        return [
            Events::QUEUE_NAME_DEMANDS,
        ];
    }
}
