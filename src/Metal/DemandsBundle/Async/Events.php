<?php

namespace Metal\DemandsBundle\Async;

final class Events
{
    public const QUEUE_NAME_DEMANDS = 'demands';

    public const EVENT_DEMAND_MODERATED = 'demands.demand_moderated';

    private function __construct()
    {
        // no-op
    }
}
