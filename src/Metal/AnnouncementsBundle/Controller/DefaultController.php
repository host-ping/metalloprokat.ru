<?php

namespace Metal\AnnouncementsBundle\Controller;

use Doctrine\DBAL\DBALException;
use Metal\AnnouncementsBundle\Entity\StatsElement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Metal\ProjectBundle\Entity\ValueObject\SourceTypeProvider;

class DefaultController extends Controller
{
    public function awayAction(Request $request)
    {
        $id = $request->query->get('id', null);

        $userAgent = $request->headers->get('User-Agent');
        if (!$id) {
            return new JsonResponse(array(
                'status' => 'error',
                'message' => 'Bad announcement id.'
                )
            );
        }

        $announcement = $this->getDoctrine()->getRepository('MetalAnnouncementsBundle:Announcement')
            ->find($id);

        if (!$announcement) {
            return new JsonResponse(array(
                    'status' => 'error',
                    'message' => 'Announcement not exist.'
                )
            );
        }

        $detector = $this->get('vipx_bot_detect.detector');
        $botMetadata = $detector->detectFromRequest($request);

        if (null !== $botMetadata) {
            return new RedirectResponse($announcement->getLink());
        }

        $user = $this->getUser();

        $city = $request->attributes->get('city');
        $category = $request->attributes->get('category');
        $sourceTypeId = $request->query->get('source_type_id', SourceTypeProvider::OTHER);
        $sourceType = SourceTypeProvider::create(in_array($sourceTypeId, SourceTypeProvider::getAllTypesIds()) ? $sourceTypeId : SourceTypeProvider::OTHER);

        if ($announcement && $announcement->isActive()) {
            $statsElement = new StatsElement();
            $statsElement->setUser($user);
            $statsElement->setAction(StatsElement::ACTION_REDIRECT);
            $statsElement->setAnnouncement($announcement);
            $statsElement->setIp($request->getClientIp());
            $statsElement->setCity($city);
            $statsElement->setCategory($category);
            $statsElement->setReferer($request->query->get('referer', ''));
            $statsElement->setSessionId($request->getSession()->getId());
            $statsElement->setUserAgent($userAgent);
            $statsElement->setSourceType($sourceType);

            $em = $this->getDoctrine()->getManager();

            $em->persist($statsElement);

            try {
                $em->flush();
            } catch (DBALException $ex) {

            }
        }

        return new RedirectResponse($announcement->getLink());
    }
}
