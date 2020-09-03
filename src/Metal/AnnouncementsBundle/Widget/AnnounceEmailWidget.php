<?php

namespace Metal\AnnouncementsBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;

class AnnounceEmailWidget extends WidgetAbstract
{
    protected function setDefaultOptions()
    {
        $this->optionsResolver
            ->setRequired(array('zone_slug'));
    }

    protected function getParametersToRender()
    {
        return array(
            'announcement' => $this
                ->getDoctrine()
                ->getManager()
                ->getRepository('MetalAnnouncementsBundle:Announcement')
                ->getEmailAnnouncement($this->options['zone_slug'])
        );
    }
}
