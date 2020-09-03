<?php

namespace Metal\TerritorialBundle\Entity;

interface TerritoryInterface
{
    const INT_SHORT_MAX = 32767;

    public function getId();

    public function getKind();

    public function getTitle();

    public function getTitleLocative();

    public function getSlug();

    //FIXME: для городов нужно отдавать getSlugWithFallback
}
