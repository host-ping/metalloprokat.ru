<?php

namespace Brouzie\Components\Indexer\Async\Processor;

use Brouzie\Components\Indexer\Async\Commands;
use Brouzie\Components\Indexer\Async\Message\IdsMessage;
use Enqueue\Consumption\Result;
use Interop\Queue\PsrContext;
use Interop\Queue\PsrMessage;

class DeleteIdsProcessor extends AbstractProcessor
{
    public function process(PsrMessage $message, PsrContext $context)
    {
        try {
            $idsMessage = IdsMessage::jsonDeserialize($message->getBody());
        } catch (\Exception $e) {
            return Result::reject($e->getMessage());
        }

        $this->indexer->delete($idsMessage->getIds());

        return self::ACK;
    }

    public static function getSubscribedProcessorName(): string
    {
        return Commands::DELETE;
    }

    public static function getSubscribedQueueName(): string
    {
        return Commands::QUEUE_NAME_INDEX;
    }
}
