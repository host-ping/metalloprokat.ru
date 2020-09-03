<?php

namespace Brouzie\Components\Indexer\Async\Processor;

use Brouzie\Components\Indexer\Indexer;
use Enqueue\Client\CommandSubscriberInterface;
use Enqueue\Consumption\QueueSubscriberInterface;
use Interop\Queue\PsrProcessor;

abstract class AbstractProcessor implements PsrProcessor, CommandSubscriberInterface, QueueSubscriberInterface
{
    protected $indexer;

    public function __construct(Indexer $indexer)
    {
        $this->indexer = $indexer;
    }

    public static function getSubscribedCommand()
    {
        return [
            'processorName' => static::getSubscribedProcessorName(),
            'queueName' => static::getSubscribedQueueName(),
            'queueNameHardcoded' => true,
        ];
    }

    public static function getSubscribedQueues()
    {
        return [static::getSubscribedQueueName()];
    }

    abstract public static function getSubscribedProcessorName(): string;

    abstract public static function getSubscribedQueueName(): string;
}
