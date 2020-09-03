<?php

namespace Brouzie\Components\Indexer\Async\Processor;

use Brouzie\Components\Indexer\Async\Commands;
use Brouzie\Components\Indexer\Async\Message\UpdateOperationMessage;
use Enqueue\Consumption\Result;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;

class UpdateProcessor extends AbstractProcessor
{
    public function process(PsrMessage $message, PsrContext $context)
    {
        try {
            $updateOperationMessage = UpdateOperationMessage::jsonDeserialize($message->getBody());
        } catch (\Exception $e) {
            return Result::reject($e->getMessage());
        }

        $this->indexer->update($updateOperationMessage->getChangeSet(), $updateOperationMessage->getCriteria());

        return self::ACK;
    }

    public static function getSubscribedProcessorName(): string
    {
        return Commands::UPDATE;
    }

    public static function getSubscribedQueueName(): string
    {
        return Commands::QUEUE_NAME_UPDATE;
    }
}
