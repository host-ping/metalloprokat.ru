<?php

namespace Metal\DemandsBundle\Notifications;

interface Notifier
{
    public function notifyOnNewDemand(int $demandId): void;
}
