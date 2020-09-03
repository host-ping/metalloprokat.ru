<?php

namespace Metal\AnnouncementsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\AnnouncementsBundle\Entity\Zone;

class ZoneStatusRepository extends EntityRepository
{
    public function attachZoneStatusesToZones(array $zones)
    {
        if (empty($zones)) {
            return;
        }

        $directedZones = array();
        foreach ($zones as $zone) {
            /* @var $zone Zone */
            $zone->setAttribute('zone_status', null);
            $directedZones[$zone->getId()] = $zone;
        }

        $zoneStatuses = $this->_em->getRepository('MetalAnnouncementsBundle:ZoneStatus')->createQueryBuilder('zs')
            ->select('PARTIAL zs.{id, status, startsAt, endsAt} AS zoneStatus, IDENTITY(zs.zone) AS zoneId')
            ->andWhere('zs.zone IN (:zones_ids)')
            ->setParameter('zones_ids', $zones)
            ->andWhere('zs.endsAt >= :now')
            ->andWhere('zs.deleted = false')
            ->setParameter('now', new \DateTime())
            ->orderBy('zs.startsAt', 'ASC')
            ->getQuery()
            ->getResult();

        $statuses = array();
        foreach ($zoneStatuses as $zoneStatus) {
            $statuses[$zoneStatus['zoneId']][] = $zoneStatus['zoneStatus'];
        }
        foreach ($zoneStatuses as $zoneStatus) {
            $zone = $directedZones[$zoneStatus['zoneId']];
            $zone->setAttribute('zone_status', $statuses[$zoneStatus['zoneId']]);
        }
    }
}
