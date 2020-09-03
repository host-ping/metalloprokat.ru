<?php

namespace Metal\GrabbersBundle\Grabber;

use Metal\GrabbersBundle\Entity\Site;

interface GrabberInterface
{
    const MESSAGE_DEMAND_IS_ALREADY_EXIST = 'Заявка была уже добавлена, обновляем дату создания и обрабатываем следующую.';

    /**
     * @param Site $site
     * @param GrabberHelper $grabberHelper
     * @param int $page
     *
     * @return GrabberResult[]
     */
    public function grab(Site $site, GrabberHelper $grabberHelper, $page = 1);

    public function getCode();
}
