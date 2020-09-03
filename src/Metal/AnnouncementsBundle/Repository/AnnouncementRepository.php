<?php

namespace Metal\AnnouncementsBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\AnnouncementsBundle\Entity\Announcement;
use Metal\AnnouncementsBundle\Entity\ValueObject\SectionProvider;
use Metal\CompaniesBundle\Entity\Company;

class AnnouncementRepository extends EntityRepository
{
    protected $emailAnnouncements;

    /**
     * @param string $zoneSlug
     *
     * @return Announcement|null
     */
    public function getEmailAnnouncement($zoneSlug)
    {
        if (null !== $this->emailAnnouncements) {
            return isset($this->emailAnnouncements[$zoneSlug]) ? $this->emailAnnouncements[$zoneSlug] : null;
        }

        $announcements = $this->createQueryBuilder('a')
            ->join('a.zone', 'az')
            ->addSelect('az')
            ->andWhere('a.isPayed = true')
            ->andWhere(':now BETWEEN a.startsAt AND a.endsAt')
            ->andWhere('az.sectionId = :section_id')
            ->setParameter('now', new \DateTime())
            ->setParameter('section_id', SectionProvider::TYPE_EMAIL)
            ->getQuery()
            ->getResult();
        /* @var $announcements Announcement[] */

        $this->emailAnnouncements = array();
        foreach ($announcements as $announcement) {
            $this->emailAnnouncements[$announcement->getZone()->getSlug()] = $announcement;
        }

        return $this->getEmailAnnouncement($zoneSlug);
    }

    public function getAnnouncementsCount(Company $company)
    {
        return $this->createQueryBuilder('a')
            ->select('COUNT(a) AS _count')
            ->where('a.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
