<?php

namespace Metal\StatisticBundle\Controller;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Metal\ProjectBundle\Entity\ValueObject\SourceTypeProvider;

use Metal\StatisticBundle\Entity\StatsElement;
use Metal\StatisticBundle\Entity\StatsSearch;
use Metal\TerritorialBundle\Entity\City;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    public function showContactAction(Request $request, City $city = null)
    {
        $objectId = $request->get('object_id');
        $source = $request->get('source');
        $objectKind = $request->get('object_kind');
        $categoryId = $request->get('category_id');

        $detector = $this->get('vipx_bot_detect.detector');
        $botMetadata = $detector->detectFromRequest($request);

        if (null !== $botMetadata) {
            return new JsonResponse(array('status' => 'success'));
        }

        if ($objectId === null || $source === null) {
            return new JsonResponse(array('status' => 'wrong parameters', 404));
        }

        $defaultCity = null;
        $companyId = null;
        $productId = null;
        switch ($objectKind) {
            case 'company':
                $company = $this->getDoctrine()->getRepository('MetalCompaniesBundle:Company')->find($objectId);
                $defaultCity = $company->getCity();
                $companyId = $objectId;
                break;
            case 'product':
                $product = $this->getDoctrine()->getRepository('MetalProductsBundle:Product')->find($objectId);
                $defaultCity = $product->getCompany()->getCity();
                $productId = $objectId;
                $companyId = $product->getCompany()->getId();
                break;

            default:
                throw new \InvalidArgumentException(sprintf('Object kind "%s" not supported', $objectKind));
        }

        if (!$defaultCity) {
            return new JsonResponse(array('status' => 'object not found', 404));
        }

        if (!$city) {
            $city = $defaultCity;
        }

        $this->createStatsElement($request, $companyId, $productId, $categoryId, $city, $source);

        return new JsonResponse(array('status' => 'success'));
    }

    protected function createStatsElement(Request $request, $companyId, $productId, $categoryId, $city, $source)
    {
        $statsElement = new StatsElement();

        $statsElement->setCompanyId($companyId);
        $statsElement->setProductId($productId);
        if ($categoryId) {
            $statsElement->setCategoryId($categoryId);
        }

        $statsElement->setCity($city);

        $statsElement->setAction(StatsElement::ACTION_VIEW_PHONE);
        $statsElement->setSourceType(SourceTypeProvider::createBySlug($source));

        $statsElement->setIp($request->getClientIp());
        $statsElement->setUserAgent($request->server->get('HTTP_USER_AGENT'));
        $statsElement->setReferer($request->server->get('HTTP_REFERER'));
        $statsElement->setSessionId($request->getSession()->getId());

        if ($this->isGranted('ROLE_USER')) {
            $statsElement->setUser($this->getUser());
        }

        $statisticHelper = $this->container->get('brouzie.helper_factory')->get('MetalStatisticBundle');
        /* @var $statisticHelper \Metal\StatisticBundle\Helper\DefaultHelper */

        $statsElementRepo = $this->container->get('doctrine')->getManager()->getRepository('MetalStatisticBundle:StatsElement');
        $statsElementRepo->insertStatsElement($statsElement, $statisticHelper->canCreateFakeStatsElement());
    }

    public function redirectSiteAction(Request $request, City $city = null)
    {
        $objectId = $request->query->get('object-id');
        $source = $request->query->get('source');
        $objectKind = $request->query->get('object-kind');
        $categoryId = $request->query->get('category-id');
        $redirectTo = $this->generateRedirectToUrl($request->query->get('url'));

        if ($objectId === null || $source === null) {
            return new JsonResponse(array('status' => 'wrong parameters', 404));
        }

        $defaultCity = null;
        $company = null;
        $productId = null;
        switch ($objectKind) {
            case 'company':
                $company = $this->getDoctrine()->getRepository('MetalCompaniesBundle:Company')->find($objectId);
                $defaultCity = $company->getCity();
                break;
            case 'product':
                $product = $this->getDoctrine()->getRepository('MetalProductsBundle:Product')->find($objectId);
                $defaultCity = $product->getCompany()->getCity();
                $productId = $objectId;
                $company = $product->getCompany();
                break;
        }

        if (!$redirectTo) {
            $redirectTo = $this->generateRedirectToUrl($company->getSite());
        }

        if (!$defaultCity) {
            throw $this->createNotFoundException('Unknown objects');
        }

        if (!$city) {
            $city = $defaultCity;
        }

        $detector = $this->get('vipx_bot_detect.detector');

        $botMetadata = $detector->detectFromRequest($request);

        if (null !== $botMetadata) {
            return new RedirectResponse($redirectTo, 301);
        }

        $statsElement = new StatsElement();

        $statsElement->setCompanyId($company->getId());
        $statsElement->setProductId($productId);
        if ($categoryId) {
            $statsElement->setCategoryId($categoryId);
        }
        $statsElement->setCity($city);

        $statsElement->setAction(StatsElement::ACTION_GO_TO_WEBSITE);
        $statsElement->setSourceType(SourceTypeProvider::createBySlug($source));

        $statsElement->setIp($request->getClientIp());
        $statsElement->setUserAgent($request->server->get('HTTP_USER_AGENT'));
        $statsElement->setReferer($request->server->get('HTTP_REFERER'));
        $statsElement->setSessionId($request->getSession()->getId());

        if ($this->isGranted('ROLE_USER')) {
            $statsElement->setUser($this->getUser());
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($statsElement);

        try {
            $em->flush();
        }
        catch(DBALException $e) {}

        return new RedirectResponse($redirectTo, 301);
    }

    private function generateRedirectToUrl($url)
    {
        if (!$url) {
            return '';
        }

        $parsedUrl = parse_url($url);

        if (empty($parsedUrl['host'])) {
            return '';
        }

        return sprintf(
            '%s://%s%s%s',
            !empty($parsedUrl['scheme']) ? $parsedUrl['scheme'] : 'http',
            idn_to_ascii($parsedUrl['host']),
            !empty($parsedUrl['path']) ? $parsedUrl['path'] : '/',
            !empty($parsedUrl['query']) ? '?'.$parsedUrl['query'] : ''
        );
    }

    public function redirectStatsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $queryBag = $request->query;
        $kind = $queryBag->get('kind');
        $redirectTo = $queryBag->get('url');

        $stats = new StatsSearch();

        if (!$kind) {
            throw new NotFoundHttpException('Wrong kind');
        }
        $stats->setKind($kind);

        $em->persist($stats);
        $em->flush();

        return new RedirectResponse($redirectTo, 301);
    }
}
