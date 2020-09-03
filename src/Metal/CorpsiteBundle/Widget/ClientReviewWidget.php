<?php

namespace Metal\CorpsiteBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Doctrine\ORM\EntityManager;

class ClientReviewWidget extends WidgetAbstract
{
    const COUNT_TO_SHOW = 5;

    protected function getParametersToRender()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $reviews = $em->getRepository('MetalCorpsiteBundle:ClientReview')->getClientReviews();

        if (count($reviews) > self::COUNT_TO_SHOW) {
            shuffle($reviews);
        }

        return compact('reviews');
    }
}
