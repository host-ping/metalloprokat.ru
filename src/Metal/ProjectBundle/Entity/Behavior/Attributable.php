<?php

namespace Metal\ProjectBundle\Entity\Behavior;

trait Attributable
{
    protected $attributes = array();

    public function getAttribute($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    public function setAttribute($key, $attributes)
    {
        $this->attributes[$key] = $attributes;
    }

    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }
}
