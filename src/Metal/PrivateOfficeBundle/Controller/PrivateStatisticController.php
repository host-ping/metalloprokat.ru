<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;

use Metal\AnnouncementsBundle\Entity\Announcement;
use Metal\DemandsBundle\Helper\DefaultHelper;
use Metal\UsersBundle\Entity\User;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PrivateStatisticController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() ")
     */
    public function viewAction(Request $request, $mode)
    {
        $queryBag = $request->query;
        $user = $this->getUser();
        /* @var $user User */
        $company = $user->getCompany();

        list($dateFrom, $dateTo) = $this->determinatePeriod($request, $company->getCreatedAt());

        $statisticReportBuilder = $this->get('metal.statistic.statistic_report_builder');
        $result = $statisticReportBuilder->getStatsResults(
            $mode,
            $dateFrom,
            $dateTo,
            array('company' => $company),
            $queryBag->get('group'),
            $queryBag->get('sort'),
            $queryBag->get('order')
        );

        return $this->render(
            $result['template'],
            array(
                'statsResultViewModel' => $result['statsResultViewModel'],
            )
        );
    }

    /**
     * @ParamConverter("announcement", class="MetalAnnouncementsBundle:Announcement", isOptional=true)
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and (not announcement or (announcement and announcement.getCompany().getId() == user.getCompany().getId()))")
     */
    public function viewMediaAction(Request $request, Announcement $announcement = null)
    {
        $queryBag = $request->query;
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $user = $this->getUser();
        /* @var $user User */
        $company = $user->getCompany();

        $announcements = $em
            ->getRepository('MetalAnnouncementsBundle:Announcement')
            ->findBy(array('company' => $company), array('endsAt' => 'DESC'));

        if (!$announcements) {
            throw $this->createNotFoundException('У компании нет баннеров.');
        }

        if (!$announcement) {
            $announcement = reset($announcements);
        }

        if ($queryBag->get('period') || $queryBag->get('date_from') || $queryBag->get('date_to')) {
            list($dateFrom, $dateTo) = $this->determinatePeriod($request, $company->getCreatedAt());
        } else {
            list($dateFrom, $dateTo) = $em
                ->getRepository('MetalStatisticBundle:StatsAnnouncementDaily')
                ->getDateRangeForAnnouncement($announcement);
        }

        $statisticReportBuilder = $this->get('metal.statistic.statistic_report_builder');
        $result = $statisticReportBuilder->getStatsResults(
            'announcement',
            $dateFrom,
            $dateTo,
            array('announcement' => $announcement),
            $queryBag->get('group'),
            $queryBag->get('sort'),
            $queryBag->get('order')
        );

        return $this->render(
            $result['template'],
            array(
                'statsResultViewModel' => $result['statsResultViewModel'],
                'announcement' => $announcement,
                'announcements' => $announcements,
            )
        );
    }

    /**
     * @param Request $request
     * @param \DateTime $availableDateFrom
     *
     * @return \DateTime[]
     */
    private function determinatePeriod(Request $request, \DateTime $availableDateFrom)
    {

        list($dateFrom, $dateTo) = DefaultHelper::determinatePeriod($request);

        if ($dateFrom < $availableDateFrom) {
            $dateFrom = $availableDateFrom;
        }

        if (!$dateTo) {
            $dateTo = new \DateTime();
        }

        return array($dateFrom, $dateTo);
    }
}
