<?php

namespace Metal\AnnouncementsBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;

class DefaultHelper extends HelperAbstract
{
    protected $announcesIds = array();

    /**
     * @return array
     */
    public function getAnnouncesIds()
    {
        return array_keys($this->announcesIds);
    }

    //TODO: del после четверга 16.2017
    public function shouldReplacePremiumAnnouncement()
    {
        $now = new \DateTime();
        $dateFrom = new \DateTime($this->container->getParameter('replace_premium_date_from'));
        $dateTo = new \DateTime($this->container->getParameter('replace_premium_date_to'));

        return $this->container->getParameter('project.family') === 'metalloprokat'
            && $now > $dateFrom && $now < $dateTo;
    }

    /**
     * @return string
     */
    public function getAnnouncesIdsAsString()
    {
        return implode('-', array_keys($this->announcesIds));
    }

    public function resetAnnouncesIds()
    {
        $this->announcesIds = array();
    }

    /**
     * @param $announceId
     */
    public function addAnnounceId($announceId)
    {
        $this->announcesIds[$announceId] = true;
    }
}
