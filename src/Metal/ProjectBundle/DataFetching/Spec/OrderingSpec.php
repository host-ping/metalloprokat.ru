<?php

namespace Metal\ProjectBundle\DataFetching\Spec;

abstract class OrderingSpec
{
    protected $orders = array();

    public function getOrders()
    {
        return $this->orders;
    }

    protected function pushOrder($order, $attributes = true)
    {
        $this->orders[$order] = $attributes;

        return $this;
    }
}
