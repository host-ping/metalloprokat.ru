<?php

namespace Metal\DemandsBundle\Notifications\Telegram;

use Metal\DemandsBundle\Notifications\DataProvider\DemandInfoProvider;
use Metal\DemandsBundle\Notifications\Notifier;

class TelegramNotifier implements Notifier
{
    private $messageFormatter;

    private $demandInfoProvider;

    private $messageSender;

    private $chatId;

    public function __construct(
        MessageFormatter $messageFormatter,
        DemandInfoProvider $demandInfoProvider,
        MessageSender $messageSender,
        string $chatId
    ) {
        $this->messageFormatter = $messageFormatter;
        $this->demandInfoProvider = $demandInfoProvider;
        $this->messageSender = $messageSender;
        $this->chatId = $chatId;
    }

    public function notifyOnNewDemand(int $demandId): void
    {
        //TODO: add logging
        $demandInfo = $this->demandInfoProvider->get($demandId);

        if (!$demandInfo->isPublic()) {
            // уведомляем только о публичных заявках
            return;
        }

        $message = $this->messageFormatter->getMessage($demandInfo);
        $this->messageSender->sendMessage($this->chatId, $message);
    }
}
