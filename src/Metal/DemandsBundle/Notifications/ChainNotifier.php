<?php

namespace Metal\DemandsBundle\Notifications;

class ChainNotifier implements Notifier
{
    private $notifiers;

    /**
     * @param Notifier[] $notifiers
     */
    public function __construct(array $notifiers)
    {
        $this->notifiers = $notifiers;
    }

    public function notifyOnNewDemand(int $demandId): void
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->notifyOnNewDemand($demandId);
        }
    }
}
