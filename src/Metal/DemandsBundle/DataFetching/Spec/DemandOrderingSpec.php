<?php

namespace Metal\DemandsBundle\DataFetching\Spec;

use Metal\ProjectBundle\DataFetching\Spec\OrderingSpec;
use Symfony\Component\HttpFoundation\Request;

class DemandOrderingSpec extends OrderingSpec
{
    public function createdAt($order = 'DESC')
    {
        return $this->pushOrder('createdAt', $order);
    }

    public function demandViewsCount($order = 'DESC')
    {
        return $this->pushOrder('demandViewsCount', $order);
    }

    public function random($seed = null)
    {
        return $this->pushOrder('random', $seed);
    }

    public function comment($seed = null)
    {
        return $this->pushOrder('random', $seed);
    }

    public function favoriteOrder($seed = null)
    {
        return $this->pushOrder('favoriteOrder', $seed);
    }

    public function favoriteComment($seed = null)
    {
        return $this->pushOrder('favoriteComment', $seed);
    }

    public static function createFromRequest(Request $request)
    {
        $order = $request->query->get('order');
        $orderBy = new DemandOrderingSpec();
        if ($order === 'popularity') {
            $orderBy->demandViewsCount();
        } else {
            $orderBy->createdAt();
        }

        return $orderBy;
    }
}
