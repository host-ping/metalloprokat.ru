<?php

namespace Metal\DemandsBundle\Notifications\Telegram;

use Metal\DemandsBundle\Notifications\DataProvider\DemandInfoProvider;
use Metal\DemandsBundle\Notifications\DataProvider\DemandSubscriptionInfoProvider;
use Metal\DemandsBundle\Notifications\DataProvider\TelegramSubscriberProvider;
use Metal\DemandsBundle\Notifications\Notifier;

class PersonalTelegramNotifier implements Notifier
{
    private $subscriberProvider;

    private $demandInfoProvider;

    private $demandSubscriptionInfoProvider;

    private $messageFormatter;

    private $messageSender;

    public function __construct(
        TelegramSubscriberProvider $subscriberProvider,
        DemandInfoProvider $demandInfoProvider,
        DemandSubscriptionInfoProvider $demandSubscriptionInfoProvider,
        MessageFormatter $messageFormatter,
        MessageSender $messageSender
    ) {
        $this->subscriberProvider = $subscriberProvider;
        $this->demandInfoProvider = $demandInfoProvider;
        $this->demandSubscriptionInfoProvider = $demandSubscriptionInfoProvider;
        $this->messageFormatter = $messageFormatter;
        $this->messageSender = $messageSender;
    }

    public function notifyOnNewDemand(int $demandId): void
    {
        $demandInfo = $this->demandInfoProvider->get($demandId);

        if (!$demandInfo->isPublic()) {
            // уведомляем только о публичных заявках
            //TODO: в перспективе можно уведомлять и о приватных заявках
            return;
        }

        $subscribers = $this->subscriberProvider->getSubscribers();

        $demandSubscriptionInfo = $this->demandSubscriptionInfoProvider->getDemandSubscriptionInfo($demandId);
        $message = $this->messageFormatter->getMessage($demandInfo);

        foreach ($subscribers as $subscriber) {
            if (!$subscriber->satisfiedBy($demandSubscriptionInfo)) {
                continue;
            }

            $this->messageSender->sendMessage($subscriber->getTelegramUserId(), $message);
        }
    }
}
