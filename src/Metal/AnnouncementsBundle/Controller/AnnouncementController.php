<?php

namespace Metal\AnnouncementsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\AnnouncementsBundle\Entity\Announcement;
use Metal\AnnouncementsBundle\Entity\StatsElement;
use Metal\NewsletterBundle\Entity\Subscriber;
use Metal\ProjectBundle\Entity\ValueObject\SourceTypeProvider;
use Metal\ProjectBundle\Http\TransparentPixelResponse;
use Metal\UsersBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AnnouncementController extends Controller
{
    public function getAnnouncementsAction(Request $request)
    {
        $announcementQuery = $request->request->get('query');
        $companyId = $this->getCompanyIdForAnnouncement($request->getClientIp());

        $zones = $this->getDoctrine()->getManager()->createQueryBuilder()
            ->select('z.id, z.slug')
            ->from('MetalAnnouncementsBundle:Zone', 'z', 'z.slug')
            ->getQuery()
            ->getResult();

        $announcementsJson = array();
        foreach ($announcementQuery as $announcementItemQuery) {
            // делаем такую проверку из-за разного кол-ва банерных зон на разных проектах
            if (!isset($zones[$announcementItemQuery['zone_slug']])) {
                continue;
            }

            $announcementItemQuery['zone_id'] = $zones[$announcementItemQuery['zone_slug']]['id'];
            $announcement = null;
            if ($announcementItemQuery['only_company_id']) {
                $companyId = $announcementItemQuery['only_company_id'];
            }

            if ($companyId) {
                $announcement = $this->findAnnouncementForCompany($companyId, $announcementItemQuery);
            }

            if (!$announcementItemQuery['only_company_id'] && !$announcement) {
                $announcement = $this->findRandomAnnouncement($announcementItemQuery, $request);
            }

            if ($announcement) {
                $announcementsJson[] = $this->serializeAnnouncement($announcement, $announcementItemQuery);
            }
        }

        return JsonResponse::create($announcementsJson);
    }

    private function getCompanyIdForAnnouncement($ip)
    {
        if ($user = $this->getUser()) {
            /* @var $user User */
            return $user->getCompany() ? $user->getCompany()->getId() : null;
        }

        if ($ip) {
            $client = $this->getDoctrine()->getRepository('MetalProjectBundle:ClientIp')->findOneBy(array('ip' => $ip));
            if ($client) {
                return $client->getCompany() ? $client->getCompany()->getId() : null;
            }
        }

        return null;
    }

    private function findAnnouncementForCompany($companyId, array $announcementItemQuery)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $announcementRepository = $em->getRepository('MetalAnnouncementsBundle:Announcement');
        $announcementQb = $announcementRepository
            ->createQueryBuilder('a')
            ->andWhere('a.company = :company')
            ->setParameter('company', $companyId)
            ->andWhere('a.isPayed = true')
            ->andWhere(':now BETWEEN a.startsAt AND a.endsAt')
            ->setParameter('now', new \DateTime())
            ->andWhere('a.zone = :zone')
            ->setParameter('zone', $announcementItemQuery['zone_id']);

        if (!empty($announcementItemQuery['category_id'])) {
            $announcementQb
                ->leftJoin('MetalAnnouncementsBundle:AnnouncementCategory', 'announcementCategory', 'WITH', 'a.id = announcementCategory.announcement')
                ->leftJoin('MetalCategoriesBundle:CategoryClosure', 'categoryClosure', 'WITH', 'categoryClosure.ancestor = announcementCategory.category')
                ->andWhere('(categoryClosure.descendant = :category OR a.showEverywhere = true)')
                ->setParameter('category', $announcementItemQuery['category_id'])
            ;
        } else {
            $announcementQb
                ->andWhere('a.showEverywhere = true');
        }

        $announcements = $announcementQb
            ->setMaxResults(Announcement::MAX_ANNOUNCEMENT_COUNT_BY_COMPANY)
            ->addOrderBy('a.dailyViewsCount')
            ->addOrderBy('a.id')
            ->getQuery()
            ->getResult();
        /* @var $announcements Announcement[] */

        if (!$announcements) {
            return null;
        }

        shuffle($announcements);

        return reset($announcements);
    }

    private function findRandomAnnouncement(array $announcementItemQuery, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $announcementRepository = $em->getRepository('MetalAnnouncementsBundle:Announcement');

        $announcementQb = $announcementRepository
            ->createQueryBuilder('a')
            ->andWhere(':now BETWEEN a.startsAt AND a.endsAt')
            ->setParameter('now', new \DateTime())
            ->andWhere('a.isPayed = true')
            ->andWhere('a.zone = :zone')
            ->setParameter('zone', $announcementItemQuery['zone_id']);

        if (!empty($announcementItemQuery['country_id'])) {
            $announcementQb
                ->leftJoin('MetalAnnouncementsBundle:AnnouncementTerritorial', 'announcementTerritory', 'WITH', 'a.id = announcementTerritory.announcement')
                ->andWhere('(announcementTerritory.country = :country OR announcementTerritory.country IS NULL or a.showEverywhereTerritory = true)')
                ->setParameter('country', $announcementItemQuery['country_id']);
        }
        else {
            $announcementQb->andWhere('a.showEverywhereTerritory = true');
        }

        if (!empty($announcementItemQuery['category_id'])) {
            $announcementQb
                ->leftJoin('MetalAnnouncementsBundle:AnnouncementCategory', 'announcementCategory', 'WITH', 'a.id = announcementCategory.announcement')
                ->leftJoin('MetalCategoriesBundle:CategoryClosure', 'categoryClosure', 'WITH', 'categoryClosure.ancestor = announcementCategory.category')
                ->andWhere('(categoryClosure.descendant = :category OR a.showEverywhere = true)')
                ->setParameter('category', $announcementItemQuery['category_id']);
        }
        else {
            $announcementQb->andWhere('a.showEverywhere = true');
        }

        $announcements = $announcementQb
            ->addOrderBy('a.dailyViewsCount')
            ->addOrderBy('a.id')
            ->setMaxResults(Announcement::MAX_ANNOUNCEMENT_COUNT_BY_RANDOM)
            ->getQuery()
            ->getResult();
        /* @var $announcements Announcement[] */

        if (!$announcements) {
            return null;
        }

        shuffle($announcements);
        $announcement = null;
        $announcement = reset($announcements);

        $this->trackAnnouncementView($announcement, $announcementItemQuery, $request);

        return $announcement;
    }

    private function trackAnnouncementView(Announcement $announcement, array $announcementItemQuery, Request $request)
    {
        $detector = $this->get('vipx_bot_detect.detector');
        $botMetadata = $detector->detectFromRequest($request);

        if (null !== $botMetadata) {
            return;
        }

        $this->createStatsElement($request, $announcement, $announcementItemQuery, $this->getUser());

        $statisticHelper = $this->container->get('brouzie.helper_factory')->get('MetalStatisticBundle');
        /* @var $statisticHelper \Metal\StatisticBundle\Helper\DefaultHelper */

        if ($statisticHelper->canCreateFakeStatsElement()) {
            $this->createStatsElement($request, $announcement, $announcementItemQuery, $this->getUser(), true);
        }
    }

    /**
     * @ParamConverter("subscriber", class="MetalNewsletterBundle:Subscriber", options={"id" = "subscriber_id"})
     */
    public function trackEmailAnnouncementAction(Request $request, Subscriber $subscriber, $announcementsIds)
    {
        $announcementRepository = $this->getDoctrine()->getManager()->getRepository('MetalAnnouncementsBundle:Announcement');
        $announcements = $announcementRepository->findBy(array('id' => explode('-', $announcementsIds)));

        foreach ($announcements as $announcement) {
            $this->createStatsElement($request, $announcement, array('source_type_id' => SourceTypeProvider::EMAIL_DEMANDS_TYPE_ID), $subscriber->getUser());
        }

        return new TransparentPixelResponse();
    }

    private function createStatsElement(Request $request, Announcement $announcement, array $announcementItemQuery, User $user = null, $fake = false)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $em->getRepository('MetalAnnouncementsBundle:Announcement')->createQueryBuilder('a')
            ->update('MetalAnnouncementsBundle:Announcement', 'a')
            ->set('a.dailyViewsCount', 'a.dailyViewsCount + 1')
            ->where('a.id = :id')
            ->setParameter('id', $announcement->getId())
            ->getQuery()
            ->execute();

        $statsElement = new StatsElement();

        $statsElement->setUser($user);
        $statsElement->setAction(StatsElement::ACTION_VIEW);
        $statsElement->setAnnouncement($announcement);
        $statsElement->setIp($request->getClientIp());
        $statsElement->setCity(isset($announcementItemQuery['city_id']) ? $em->getReference('MetalTerritorialBundle:City', $announcementItemQuery['city_id']) : null);
        $statsElement->setCategory(isset($announcementItemQuery['category_id']) ? $em->getReference('MetalCategoriesBundle:Category', $announcementItemQuery['category_id']) : null);
        $statsElement->setReferer(!empty($announcementItemQuery['referrer']) ? $announcementItemQuery['referrer'] : null);
        $statsElement->setSessionId($request->getSession()->getId());
        $statsElement->setUserAgent($request->headers->get('USER_AGENT'));
        $statsElement->setSourceTypeId($announcementItemQuery['source_type_id']);
        $statsElement->setFake($fake);

        $data = $statsElement->toArray();

        $em->getConnection()->executeUpdate(
            'INSERT IGNORE INTO announcement_stats_element (' . implode(', ', array_keys($data)) . ')'.
            ' VALUES (' . implode(', ', array_fill(0, count($data), '?')) . ')',
            array_values($data)
        );
    }

    private function serializeAnnouncement(Announcement $announcement, array $announcementItemQuery)
    {
        $webPath = $this->container->getParameter('kernel.environment') == 'dev' ? $announcement->getDevWebPath() : $announcement->getEmbedWebPath();
        $fallbackWebPath = $announcement->getFallbackWebPath();

        return array(
            'elementId' => $announcementItemQuery['element_id'],
            'imageUrl' => $this->container->get('templating.helper.assets')->getUrl($webPath, null, $announcement->getVersion()),
            'fallbackImageUrl' => $fallbackWebPath,
            'url' => $this->generateUrl(
                'MetalAnnouncementsBundle:Default:away',
                array(
                    'id' => $announcement->getId(),
                    'source_type_id' => $announcementItemQuery['source_type_id'],
                    'referer' => isset($announcementItemQuery['referer']) ? $announcementItemQuery['referer'] : null
                )
            ),
            'isFlash' => $announcement->isFlash(),
            'isHtml' => $announcement->isHtml(),
            'isZip' => $announcement->isZip(),
            'width' => $announcement->getZone()->getWidth(),
            'height' => $announcement->getZone()->getHeight(),
            'isBackground' => $announcement->getZone()->getSlug() === 'background',
            'addOnAnnouncement' => $announcement->getZone()->getSlug() === 'add-on-layout',
            'isResizable' => $announcement->isResizable(),
            'zoneSlug' => $announcement->getZone()->getSlug()
        );
    }
}
