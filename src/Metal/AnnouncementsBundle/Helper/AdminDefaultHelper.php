<?php

namespace Metal\AnnouncementsBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\AnnouncementsBundle\Entity\Announcement;

class AdminDefaultHelper extends HelperAbstract {

    public function getAnnouncementSizePerZone(Announcement $announcement)
    {
        $announcementZone = $announcement->getZone();

        return array(
            'width' => $announcementZone ? $announcementZone->getWidth() : 1,
            'height' => $announcementZone ? $announcementZone->getHeight() : 1
        );
    }

    public function getAnnouncementDimensions(Announcement $announcement)
    {
        $announcementZone = $announcement->getZone();

        $announcementWidth = $announcementZone ? $announcementZone->getWidth() : 1;
        $announcementHeight = $announcementZone ? $announcementZone->getHeight() : 1;

        if ($announcementWidth > 300) {
            $resizebleWidth = 300;
            $resizebleHeight = (300/$announcementWidth)*$announcementHeight;
        }

         if ($announcementHeight > 300) {
             $resizebleHeight = 300;
             $resizebleWidth = (300/$announcementHeight)*$announcementWidth;
         }

        return array(
            'width' => isset($resizebleWidth) ? (int)$resizebleWidth : $announcementWidth,
            'height' => isset($resizebleHeight) ? (int)$resizebleHeight : $announcementHeight
        );
    }
} 